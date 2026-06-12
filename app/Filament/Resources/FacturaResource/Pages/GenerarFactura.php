<?php

namespace App\Filament\Resources\FacturaResource\Pages;

use App\Filament\Resources\FacturaResource;
use App\Models\CajaApertura;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\InventarioProductos;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenerarFactura extends Page
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = FacturaResource::class;
    protected static string $view = 'filament.resources.factura-resource.pages.generar-factura';
    protected static ?string $title = 'Generar Orden de Venta';

    public ?array $data = [];
    public array $lineasVenta = [];
    public float $subtotal = 0;
    public float $impuestos = 0;
    public float $total = 0;

    public function mount(?int $record = null): void
    {
        $aperturaId = session('apertura_id');

        $cajaAbierta = CajaApertura::where('id', $aperturaId)
            ->where('user_id', Auth::id())
            ->where('estado', 'ABIERTA')
            ->exists();

        if (! $cajaAbierta) {
            session()->forget('apertura_id');
            Notification::make()
                ->title('Acceso Denegado')
                ->body('No tienes una caja activa para facturar. Por favor, abre una caja primero.')
                ->danger()
                ->send();
            $this->redirect(FacturaResource::getUrl('index'));
            return;
        }

        if ($record) {
            $factura = Factura::with('detalles.producto.producto')->findOrFail($record);

            $this->form->fill([
                'nombre_cliente' => $factura->nombre_cliente ?? 'Consumidor Final',
                'tipo_precio' => 'precio_detalle',
                'cantidad_busqueda' => 1,
                'usar_cai' => true,
            ]);

            $this->lineasVenta = $factura->detalles->mapWithKeys(function ($det) {
                return [
                    $det->producto_id => [
                        'inventario_id' => $det->producto_id,
                        'sku' => $det->sku_snapshot ?? ($det->producto->producto->sku ?? null),
                        'nombre' => $det->nombre_producto_snapshot ?? ($det->producto->producto->nombre ?? null),
                        'precio_unitario' => $det->precio_unitario,
                        'cantidad' => $det->cantidad,
                        'isv_producto' => $det->isv_aplicado ?? 0,
                        'descuento_aplicado' => $det->descuento_aplicado ?? 0,
                        'tipo_precio_key' => $det->tipo_precio_utilizado ?? 'precio_detalle',
                        'tipo_precio_label' => 'Detalle',
                    ],
                ];
            })->toArray();

            $this->calcularTotales();
            session(['factura_pendiente_id' => $record]);
            return;
        }

        session()->forget('factura_pendiente_id');

        $this->form->fill([
            'nombre_cliente' => 'Consumidor Final',
            'tipo_precio' => 'precio_detalle',
            'cantidad_busqueda' => 1,
            'usar_cai' => true,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(12)->schema([
                TextInput::make('nombre_cliente')
                    ->label('Cliente')
                    ->required()
                    ->default('Consumidor Final')
                    ->columnSpan(5),

                Select::make('tipo_precio')
                    ->label('Tipo de Precio')
                    ->options([
                        'precio_detalle' => 'Detalle',
                        'precio_mayorista' => 'Mayorista',
                        'precio_promocion' => 'Promoción',
                    ])
                    ->default('precio_detalle')
                    ->required()
                    ->columnSpan(3),
            ]),
            Grid::make(12)->schema([
                TextInput::make('cantidad_busqueda')
                    ->label('Cantidad')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->columnSpan(3),

                TextInput::make('sku_busqueda')
                    ->label('Buscar por Código de Barras / SKU')
                    ->placeholder('Escanee o ingrese el código...')
                    ->autofocus()
                    ->live(debounce: 500)
                    ->extraAttributes(['wire:keydown.enter.prevent' => 'agregarProducto'])
                    ->columnSpan(6),
            ]),
        ])->statePath('data');
    }

    public function agregarProducto(): void
    {
        $data = $this->form->getState();
        $sku = $data['sku_busqueda'] ?? null;
        $cantidad = (int) ($data['cantidad_busqueda'] ?? 1);
        $tipoPrecio = $data['tipo_precio'] ?? 'precio_detalle';

        if (empty($sku)) {
            return;
        }

        $query = InventarioProductos::query();
        if (auth()->user()->hasRole('root')) {
            $query->withoutGlobalScopes();
        }

        $inventarioProducto = $query
            ->whereHas('producto', fn ($q) => $q->where('sku', $sku)->orWhere('codigo', $sku))
            ->with('producto')
            ->first();

        if (! $inventarioProducto) {
            Notification::make()->danger()->title('Producto no encontrado')->send();
            return;
        }

        if ($inventarioProducto->cantidad < $cantidad) {
            Notification::make()->warning()
                ->title('Stock Insuficiente')
                ->body("Solo hay {$inventarioProducto->cantidad} unidades.")
                ->send();
            return;
        }

        $precioOriginal = $inventarioProducto->{$tipoPrecio};
        $precioConDescuento = round($precioOriginal, 2);

        $productoId = $inventarioProducto->id;
        if (isset($this->lineasVenta[$productoId])) {
            $this->lineasVenta[$productoId]['cantidad'] += $cantidad;
        } else {
            $this->lineasVenta[$productoId] = [
                'inventario_id' => $productoId,
                'nombre' => $inventarioProducto->producto->nombre,
                'sku' => $inventarioProducto->producto->sku,
                'precio_unitario' => $precioConDescuento,
                'cantidad' => $cantidad,
                'tipo_precio_key' => $tipoPrecio,
                'tipo_precio_label' => $this->getTipoPrecioLabel($tipoPrecio),
                'isv_producto' => $inventarioProducto->producto->isv ?? 0,
                'descuento_aplicado' => 0,
            ];
        }

        $this->form->fill([
            'sku_busqueda' => '',
            'cantidad_busqueda' => 1,
            'nombre_cliente' => $data['nombre_cliente'] ?? 'Consumidor Final',
            'tipo_precio' => $tipoPrecio,
        ]);

        $this->calcularTotales();
    }

    private function getTipoPrecioLabel(string $tipoPrecio): string
    {
        return match ($tipoPrecio) {
            'precio_mayorista' => 'Mayorista',
            'precio_promocion' => 'Promoción',
            default => 'Detalle',
        };
    }

    public function calcularTotales(): void
    {
        $this->subtotal = 0;
        $this->impuestos = 0;

        foreach ($this->lineasVenta as $linea) {
            $subtotalLinea = $linea['precio_unitario'] * $linea['cantidad'];
            $impuestoLinea = $subtotalLinea * (($linea['isv_producto'] ?? 0) / 100);
            $this->subtotal += $subtotalLinea;
            $this->impuestos += $impuestoLinea;
        }

        $this->total = $this->subtotal + $this->impuestos;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('Agregar Métodos de Pago')
                ->color('success')
                ->icon('heroicon-o-document-check')
                ->requiresConfirmation()
                ->action('submit')
                ->disabled(empty($this->lineasVenta)),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if (empty($this->lineasVenta) || empty($data['nombre_cliente'])) {
            Notification::make()
                ->danger()
                ->title('Faltan Datos')
                ->body('Debe indicar un cliente y agregar productos.')
                ->send();
            return;
        }

        try {
            DB::transaction(function () use ($data) {
                $pendienteId = session('factura_pendiente_id');

                $factura = $pendienteId ? Factura::findOrFail($pendienteId) : new Factura();
                $esNueva = ! $factura->exists;

                $empleado = auth()->user()->empleado;
                if (! $empleado) {
                    throw new \Exception('No se encontró un empleado asociado al usuario actual.');
                }

                $factura->nombre_cliente = $data['nombre_cliente'];
                $factura->empleado_id = $empleado->id;
                $factura->fecha_factura = now();
                $factura->estado = 'Pendiente';
                $factura->subtotal = $this->subtotal;
                $factura->impuestos = $this->impuestos;
                $factura->total = $this->total;
                $factura->cai_id = null;
                $factura->apertura_id = session('apertura_id');

                if ($esNueva) {
                    $factura->numero_factura = 'TEMP';
                    $factura->save();
                    $factura->update(['numero_factura' => (string) $factura->id]);
                    session(['factura_pendiente_id' => $factura->id]);
                } else {
                    $factura->save();
                    $factura->detalles()->delete();
                }

                foreach ($this->lineasVenta as $linea) {
                    $inventario = InventarioProductos::find($linea['inventario_id']);
                    $costo = $inventario?->precio_costo ?? 0;
                    $precioUnitario = $linea['precio_unitario'];

                    DetalleFactura::create([
                        'factura_id' => $factura->id,
                        'producto_id' => $linea['inventario_id'],
                        'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $precioUnitario,
                        'descuento_aplicado' => 0,
                        'sub_total' => $linea['cantidad'] * $precioUnitario,
                        'isv_aplicado' => $linea['isv_producto'] ?? 0,
                        'costo_unitario' => $costo,
                        'utilidad_unitaria' => round($precioUnitario - $costo, 2),
                        'tipo_precio_utilizado' => $linea['tipo_precio_key'] ?? null,
                        'origen_descuento' => 'ninguno',
                        'nombre_producto_snapshot' => $inventario->producto->nombre ?? null,
                        'sku_snapshot' => $inventario->producto->sku ?? null,
                    ]);

                    $inventario?->decrement('cantidad', $linea['cantidad']);
                }

                Notification::make()
                    ->success()
                    ->title('Orden actualizada como pendiente')
                    ->body('Ahora puedes registrar el pago para asignar CAI y número.')
                    ->send();
            });

            $this->redirect(FacturaResource::getUrl('view', ['record' => session('factura_pendiente_id')]));
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error al generar la factura')
                ->body($e->getMessage())
                ->send();
        }
    }
}
