<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CajaAperturaResource\Pages;
use App\Models\CajaApertura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CajaAperturaResource extends Resource
{
    protected static ?string $model = CajaApertura::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Ventas';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('monto_inicial')
                ->required()
                ->numeric()
                ->prefix('L')
                ->default(2000.00),

            Forms\Components\TextInput::make('monto_final_calculado')
                ->numeric()
                ->prefix('L')
                ->disabled()
                ->visibleOn('edit'),

            Forms\Components\DateTimePicker::make('fecha_cierre')
                ->disabled()
                ->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto_inicial')
                    ->money('LPS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_apertura')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_cierre')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ABIERTA' => 'success',
                        'CERRADA' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('ir_a_facturar')
                        ->label('Ir a Facturar')
                        ->icon('heroicon-o-document-plus')
                        ->color('success')
                        ->visible(fn (CajaApertura $record): bool => $record->estado === 'ABIERTA' && $record->user_id === auth()->id())
                        ->action(function (CajaApertura $record) {
                            session(['apertura_id' => $record->id]);
                            return redirect(FacturaResource::getUrl('generar-factura'));
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCajaAperturas::route('/'),
            'create' => Pages\CreateCajaApertura::route('/create'),
            'edit' => Pages\EditCajaApertura::route('/{record}/edit'),
        ];
    }
}
