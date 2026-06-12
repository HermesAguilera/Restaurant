<?php

namespace App\Filament\Pages;

use App\Models\OrdenRestaurante;
use App\Services\OrderHistoryService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HistorialPedidos extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Historial de Pedidos';
    protected static ?string $navigationGroup = 'Ventas';
    protected static string $view = 'filament.pages.historial-pedidos';

    public string $tipo_periodo = 'diario';
    public string $fecha_referencia;
    public array $resumen = [];

    public function mount(): void
    {
        abort_unless(Auth::check() && (Auth::user()->hasRole('root') || Auth::user()->can('ventas_ver')), 403);

        $this->fecha_referencia = now()->toDateString();
        $this->resumen = $this->calcularResumen();
    }

    public function updatedTipoPeriodo(): void
    {
        $this->resumen = $this->calcularResumen();
        $this->resetTablePage();
    }

    public function updatedFechaReferencia(): void
    {
        $this->resumen = $this->calcularResumen();
        $this->resetTablePage();
    }

    protected function filteredQuery(): Builder
    {
        $query = OrdenRestaurante::query();

        if (filled($this->tipo_periodo) && filled($this->fecha_referencia)) {
            try {
                return (new OrderHistoryService())->applyPeriodFilter(
                    $query,
                    $this->tipo_periodo,
                    $this->fecha_referencia
                );
            } catch (\Throwable $e) {
                Notification::make()
                    ->title('Filtro inválido')
                    ->body('No se pudo aplicar el filtro de periodo. Verifica la fecha seleccionada.')
                    ->danger()
                    ->send();
            }
        }

        return $query;
    }

    protected function calcularResumen(): array
    {
        $ordenes = $this->filteredQuery()->with('detalles')->get();

        return [
            'total_ordenes' => $ordenes->count(),
            'total_monto' => (float) $ordenes->sum('total'),
            'entregadas' => $ordenes->filter(fn (OrdenRestaurante $orden) => filled($orden->entregado_at))->count(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->filteredQuery())
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('nombre_cliente')->label('Cliente')->searchable(),
                TextColumn::make('mesa')->label('Mesa')->searchable(),
                TextColumn::make('notas')->label('Notas')->searchable()->limit(40)->placeholder('Sin notas'),
                TextColumn::make('total')->label('Total')->money('HNL'),
                TextColumn::make('entregado_at')->label('Entregado')->dateTime('d/m/Y h:i A')->placeholder('Pendiente'),
                TextColumn::make('fecha_orden')->label('Fecha')->date(),
            ])
            ->actions([
                Action::make('verDetalle')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (OrdenRestaurante $record) => "Pedido #{$record->id}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (OrdenRestaurante $record) {
                        $record->loadMissing('detalles.platillo');

                        return view('filament.pages.partials.historial-pedidos-detalle', [
                            'record' => $record,
                        ]);
                    }),
                Action::make('imprimirDetalle')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->url(fn (OrdenRestaurante $record) => route('historial-pedidos.imprimir', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('fecha_orden', 'desc')
            ->emptyStateHeading('No hay pedidos para mostrar')
            ->emptyStateDescription('Puedes ajustar el filtro de periodo o la búsqueda para encontrar pedidos.')
            ->searchable();
    }
}
