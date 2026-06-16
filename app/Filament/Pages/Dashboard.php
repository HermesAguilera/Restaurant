<?php

namespace App\Filament\Pages;

use App\Models\OrdenRestaurante;
use App\Models\OrdenRestauranteDetalle;
use App\Models\Platillo;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Caja / POS';
    protected static ?string $title = 'Caja / POS';
    protected static ?string $slug = 'caja-pos';
    protected static string $view = 'filament.pages.caja-dashboard';

    public array $carrito = [];
    public string $nombre_cliente = 'Consumidor Final';
    public string $notas = '';
    public string $busqueda = '';
    public string $filtro_categoria = '';
    public string $filtro_seccion = 'comida';
    public string $subfiltro_cocina = 'todos';
    public string $tipo_orden = 'restaurante';
    public int $numero_personas = 1;
    public string $mesa = '';
    public mixed $ordenDetalle = null;

    public function getPlatillosProperty()
    {
        $query = Platillo::where('disponible', true)
            ->where('tipo', $this->filtro_seccion);

        if ($this->filtro_seccion === 'comida' && $this->subfiltro_cocina !== 'todos') {
            $query->where('seccion', $this->subfiltro_cocina);
        }

        if ($this->busqueda) {
            $query->where('nombre', 'like', '%' . $this->busqueda . '%');
        }

        if ($this->filtro_categoria) {
            $query->where('categoria', $this->filtro_categoria);
        }

        return $query->orderBy('categoria')->orderBy('nombre')->get();
    }

    public function getCategoriasProperty(): array
    {
        return Platillo::where('disponible', true)
            ->where('tipo', $this->filtro_seccion)
            ->distinct()
            ->pluck('categoria')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getOrdenesPendientesProperty()
    {
        $query = OrdenRestaurante::with('detalles.platillo')
            ->whereDate('fecha_orden', now()->toDateString())
            ->orderBy('numero_dia', 'asc');

        $query->whereNull('entregado_at');

        return $query->get();
    }

    public function getOrdenesEntregadasProperty()
    {
        $query = OrdenRestaurante::with('detalles.platillo')
            ->whereDate('fecha_orden', now()->toDateString())
            ->orderBy('updated_at', 'desc');

        $query->whereNotNull('entregado_at');

        return $query->get();
    }

    public function agregarItem(int $platilloId): void
    {
        $platillo = Platillo::find($platilloId);
        if (! $platillo) {
            return;
        }

        $key = (string) $platilloId;

        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
            return;
        }

        $this->carrito[$key] = [
            'id' => $platillo->id,
            'nombre' => $platillo->nombre,
            'precio' => (float) $platillo->precio,
            'cantidad' => 1,
        ];
    }

    public function incrementar(string $key): void
    {
        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
        }
    }

    public function decrementar(string $key): void
    {
        if (! isset($this->carrito[$key])) {
            return;
        }

        $this->carrito[$key]['cantidad']--;

        if ($this->carrito[$key]['cantidad'] <= 0) {
            unset($this->carrito[$key]);
        }
    }

    public function remover(string $key): void
    {
        unset($this->carrito[$key]);
    }

    public function limpiarCarrito(): void
    {
        $this->carrito = [];
        $this->nombre_cliente = 'Consumidor Final';
        $this->notas = '';
        $this->numero_personas = 1;
        $this->tipo_orden = 'restaurante';
        $this->mesa = '';
    }

    public function getTotalProperty(): float
    {
        return collect($this->carrito)->sum(fn ($item) => $item['precio'] * $item['cantidad']);
    }

    public function enviarACocina(): void
    {
        if (empty($this->carrito)) {
            Notification::make()
                ->title('El carrito está vacío')
                ->warning()
                ->send();

            return;
        }

        try {
            [$orden, $numeroDia] = DB::transaction(function () {
                $ultimoNumeroDia = OrdenRestaurante::whereDate('fecha_orden', now()->toDateString())
                    ->max('numero_dia');

                $numeroDia = ($ultimoNumeroDia ?? 0) + 1;

                $orden = OrdenRestaurante::create([
                    'nombre_cliente' => $this->nombre_cliente ?: 'Consumidor Final',
                    'mesa' => filled($this->mesa) ? trim($this->mesa) : null,
                    'notas' => $this->notas,
                    'total' => $this->total,
                    'numero_dia' => $numeroDia,
                    'fecha_orden' => now()->toDateString(),
                ]);

                foreach ($this->carrito as $item) {
                    OrdenRestauranteDetalle::create([
                        'orden_restaurante_id' => $orden->id,
                        'platillo_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        'subtotal' => $item['precio'] * $item['cantidad'],
                        'tipo_orden' => $this->tipo_orden,
                        'numero_personas' => $this->numero_personas,
                    ]);
                }

                return [$orden, $numeroDia];
            });

            $this->limpiarCarrito();

            Notification::make()
                ->title("✅ Orden #{$numeroDia} enviada a cocina")
                ->success()
                ->duration(3000)
                ->send();
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('No se pudo enviar la orden')
                ->body('Revisa el registro del sistema e intenta nuevamente.')
                ->danger()
                ->send();
        }
    }

    public function marcarComoEntregada($ordenId): void
    {
        OrdenRestaurante::where('id', $ordenId)->update([
            'entregado_at' => now(),
        ]);

        Notification::make()
            ->title('Orden entregada al cliente')
            ->success()
            ->send();
    }

    public function viewOrderAction(): Action
    {
        return Action::make('viewOrder')
            ->label('Ver detalle')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn (array $arguments): string => 'Pedido #' . ($arguments['orderId'] ?? ''))
            ->modalDescription('Detalle del pedido y sus platillos.')
            ->modalIcon('heroicon-o-document-text')
            ->modalIconColor('primary')
            ->modalWidth(MaxWidth::FiveExtraLarge)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar')
            ->modalContent(function (array $arguments) {
                $orden = OrdenRestaurante::with('detalles.platillo')->findOrFail($arguments['orderId']);

                return view('filament.pages.partials.historial-pedidos-detalle', [
                    'record' => $orden,
                ]);
            });
    }

    public function editOrderAction(): Action
    {
        return Action::make('editOrder')
            ->label('Editar pedido')
            ->icon('heroicon-o-pencil-square')
            ->color('primary')
            ->slideOver()
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->closeModalByClickingAway(false)
            ->modalIcon('heroicon-o-pencil-square')
            ->modalIconColor('primary')
            ->modalSubmitActionLabel('Guardar cambios')
            ->modalCancelActionLabel('Cancelar')
            ->fillForm(function (array $arguments): array {
                $orden = OrdenRestaurante::with('detalles.platillo')->findOrFail($arguments['orderId']);
                $primerDetalle = $orden->detalles->first();

                return [
                    'nombre_cliente' => $orden->nombre_cliente,
                    'mesa' => $orden->mesa,
                    'notas' => $orden->notas,
                    'tipo_orden' => $primerDetalle?->tipo_orden ?? 'restaurante',
                    'numero_personas' => (int) ($primerDetalle?->numero_personas ?? 1),
                    'items' => $orden->detalles->map(function ($detalle) {
                        return [
                            'platillo_id' => $detalle->platillo_id,
                            'cantidad' => (int) $detalle->cantidad,
                            'precio_unitario' => (float) $detalle->precio_unitario,
                        ];
                    })->values()->all(),
                ];
            })
            ->form([
                Section::make('Datos generales')
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2])->schema([
                            TextInput::make('nombre_cliente')
                                ->label('Cliente')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('mesa')
                                ->label('Mesa')
                                ->placeholder('Opcional')
                                ->maxLength(255),
                            Select::make('tipo_orden')
                                ->label('Tipo de orden')
                                ->options([
                                    'restaurante' => 'Comer aquí',
                                    'llevar' => 'Para llevar',
                                ])
                                ->required(),
                            TextInput::make('numero_personas')
                                ->label('Número de personas')
                                ->numeric()
                                ->minValue(1)
                                ->required(),
                            Textarea::make('notas')
                                ->label('Notas')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columns(1),

                Section::make('Platillos del pedido')
                    ->description('Puedes cambiar cantidades o reemplazar platillos desde aquí.')
                    ->schema([
                        Repeater::make('items')
                            ->label(false)
                            ->addActionLabel('Agregar platillo')
                            ->reorderable(false)
                            ->defaultItems(1)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 12])->schema([
                                    Select::make('platillo_id')
                                        ->label('Platillo')
                                        ->options(fn () => Platillo::query()
                                            ->where('disponible', true)
                                            ->orderBy('nombre')
                                            ->pluck('nombre', 'id')
                                            ->all())
                                        ->searchable()
                                        ->required()
                                        ->columnSpan(['default' => 1, 'md' => 7])
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set): void {
                                            $set('precio_unitario', (float) (Platillo::find($state)?->precio ?? 0));
                                        }),
                                    TextInput::make('cantidad')
                                        ->label('Cantidad')
                                        ->numeric()
                                        ->minValue(1)
                                        ->required()
                                        ->columnSpan(['default' => 1, 'md' => 2]),
                                    TextInput::make('precio_unitario')
                                        ->label('Precio')
                                        ->numeric()
                                        ->prefix('L.')
                                        ->required()
                                        ->columnSpan(['default' => 1, 'md' => 3]),
                                ]),
                            ]),
                    ])
                    ->columns(1),
            ])
            ->action(function (array $data, array $arguments): void {
                $ordenId = $arguments['orderId'] ?? null;

                if (! $ordenId) {
                    Notification::make()
                        ->title('Pedido inválido')
                        ->danger()
                        ->send();

                    return;
                }

                $items = array_values(array_filter($data['items'] ?? [], function (array $item): bool {
                    return ! empty($item['platillo_id']) && (int) ($item['cantidad'] ?? 0) > 0;
                }));

                if (empty($items)) {
                    Notification::make()
                        ->title('La orden no puede quedar vacía')
                        ->warning()
                        ->send();

                    return;
                }

                try {
                    DB::transaction(function () use ($data, $items, $ordenId): void {
                        $orden = OrdenRestaurante::lockForUpdate()->findOrFail($ordenId);

                        $total = collect($items)->sum(fn (array $item): float => (float) $item['precio_unitario'] * (int) $item['cantidad']);

                        $orden->update([
                            'nombre_cliente' => $data['nombre_cliente'] ?: 'Consumidor Final',
                            'mesa' => filled($data['mesa'] ?? null) ? trim($data['mesa']) : null,
                            'notas' => $data['notas'] ?? '',
                            'total' => $total,
                        ]);

                        $orden->detalles()->delete();

                        foreach ($items as $item) {
                            OrdenRestauranteDetalle::create([
                                'orden_restaurante_id' => $orden->id,
                                'platillo_id' => $item['platillo_id'],
                                'cantidad' => $item['cantidad'],
                                'precio_unitario' => $item['precio_unitario'],
                                'subtotal' => (float) $item['precio_unitario'] * (int) $item['cantidad'],
                                'tipo_orden' => $data['tipo_orden'] ?? 'restaurante',
                                'numero_personas' => $data['numero_personas'] ?? 1,
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Pedido actualizado')
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    report($e);

                    Notification::make()
                        ->title('No se pudo actualizar el pedido')
                        ->body('Revisa el registro del sistema e intenta nuevamente.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function deleteOrderAction(): Action
    {
        return Action::make('deleteOrder')
            ->label('Eliminar pedido')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Eliminar orden')
            ->modalDescription('Esta acción quitará la orden de progreso y cocina. No se enviará a entregadas.')
            ->modalSubmitActionLabel('Sí, eliminar')
            ->modalCancelActionLabel('Cancelar')
            ->modalIcon('heroicon-o-trash')
            ->modalIconColor('danger')
            ->action(function (array $arguments): void {
                $ordenId = $arguments['orderId'] ?? null;

                if (! $ordenId) {
                    return;
                }

                try {
                    DB::transaction(function () use ($ordenId) {
                        $orden = OrdenRestaurante::with('detalles')->findOrFail($ordenId);
                        $orden->detalles()->delete();
                        $orden->delete();
                    });

                    Notification::make()
                        ->title('Orden eliminada')
                        ->body('La orden fue eliminada correctamente.')
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    report($e);

                    Notification::make()
                        ->title('No se pudo eliminar la orden')
                        ->body('Revisa el registro del sistema e intenta nuevamente.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function eliminarOrden($ordenId): void
    {
        try {
            DB::transaction(function () use ($ordenId) {
                $orden = OrdenRestaurante::with('detalles')->findOrFail($ordenId);
                $orden->detalles()->delete();
                $orden->delete();
            });

            Notification::make()
                ->title('Orden eliminada')
                ->body('La orden fue eliminada de progreso y de cocina.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('No se pudo eliminar la orden')
                ->body('Revisa el registro del sistema e intenta nuevamente.')
                ->danger()
                ->send();
        }
    }
}
