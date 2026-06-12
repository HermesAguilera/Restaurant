<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Session;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationLabel = 'Facturas';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'facturas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información General')
                ->schema([
                    Forms\Components\TextInput::make('nombre_cliente')
                        ->label('Cliente')
                        ->disabled(),

                    Forms\Components\Select::make('empleado_id')
                        ->label('Vendedor')
                        ->options(
                            \App\Models\Empleado::with('persona')
                                ->get()
                                ->mapWithKeys(function ($empleado) {
                                    $nombre = trim(($empleado->persona->primer_nombre ?? '') . ' ' . ($empleado->persona->primer_apellido ?? ''));
                                    return [$empleado->id => $nombre !== '' ? $nombre : ('Empleado #' . $empleado->id)];
                                })
                        )
                        ->disabled(),

                    Forms\Components\DatePicker::make('fecha_factura')->disabled(),
                    Forms\Components\Select::make('estado')
                        ->options([
                            'Pendiente' => 'Pendiente',
                            'Pagada' => 'Pagada',
                            'Anulada' => 'Anulada',
                            'Vencida' => 'Vencida',
                        ])
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Totales')
                ->schema([
                    Forms\Components\TextInput::make('subtotal')->numeric()->prefix('L.')->disabled(),
                    Forms\Components\TextInput::make('impuestos')->numeric()->prefix('L.')->disabled(),
                    Forms\Components\TextInput::make('total')->numeric()->prefix('L.')->disabled(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N° Factura')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nombre_cliente')->label('Cliente'),
                Tables\Columns\TextColumn::make('empleado.persona.primer_nombre')->label('Vendedor'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Pagada' => 'success',
                        'Anulada' => 'danger',
                        'Vencida' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('total')->money('HNL')->sortable(),
                Tables\Columns\TextColumn::make('fecha_factura')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->multiple()
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Pagada' => 'Pagada',
                        'Anulada' => 'Anulada',
                        'Vencida' => 'Vencida',
                    ]),

                TernaryFilter::make('con_cai')
                    ->label('¿Con CAI?')
                    ->placeholder('Todas')
                    ->trueLabel('Con CAI')
                    ->falseLabel('Sin CAI (Orden)')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('cai_id'),
                        false: fn (Builder $q) => $q->whereNull('cai_id'),
                        blank: fn (Builder $q) => $q
                    ),

                Filter::make('fecha_factura')
                    ->label('Fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $badges = [];
                        if (! empty($data['desde'])) {
                            $badges[] = 'Desde ' . Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if (! empty($data['hasta'])) {
                            $badges[] = 'Hasta ' . Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $badges;
                    })
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['desde'] ?? null, fn ($qq, $d) => $qq->whereDate('fecha_factura', '>=', $d))
                            ->when($data['hasta'] ?? null, fn ($qq, $h) => $qq->whereDate('fecha_factura', '<=', $h));
                    }),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturas::route('/'),
            'edit' => Pages\EditFactura::route('/{record}/edit'),
            'generar-factura' => Pages\GenerarFactura::route('/generar'),
            'view' => Pages\ViewFactura::route('/{record}'),
            'edit-pendiente' => Pages\GenerarFactura::route('/{record}/editar-pendiente'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $aperturaId = Session::get('apertura_id');
        $query = parent::getEloquentQuery()->with(['empleado.persona']);

        if (! $aperturaId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('apertura_id', $aperturaId)->orderByDesc('id');
    }
}
