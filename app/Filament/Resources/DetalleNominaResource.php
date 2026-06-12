<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetalleNominaResource\Pages;
use App\Models\DetalleNominas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class DetalleNominaResource extends Resource
{
    protected static ?string $model = DetalleNominas::class;
    protected static ?string $navigationGroup = 'Nominas';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Historial de Pagos';
    protected static ?string $modelLabel = 'Historial de Pago';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('nomina_id')
                ->label('Nómina')
                ->relationship('nomina', 'id')
                ->required(),

            Forms\Components\Select::make('empleado_id')
                ->label('Empleado')
                ->relationship('empleado', 'nombre')
                ->required(),

            Forms\Components\TextInput::make('sueldo_bruto')->label('Sueldo bruto')->numeric()->required(),
            Forms\Components\TextInput::make('deducciones')->label('Deducciones')->numeric()->required(),
            Forms\Components\TextInput::make('percepciones')->label('Percepciones')->numeric()->required(),
            Forms\Components\TextInput::make('sueldo_neto')->label('Sueldo neto')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomina.id')->label('Nómina'),
                Tables\Columns\TextColumn::make('empleado.nombre')->label('Empleado'),
                Tables\Columns\TextColumn::make('sueldo_bruto')->label('Sueldo Bruto'),
                Tables\Columns\TextColumn::make('deducciones')->label('Deducciones'),
                Tables\Columns\TextColumn::make('percepciones')->label('Percepciones'),
                Tables\Columns\TextColumn::make('sueldo_neto')->label('Sueldo Neto'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->icon('heroicon-o-eye'),
            ])
            ->headerActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetalleNominas::route('/'),
            'edit' => Pages\EditDetalleNomina::route('/{record}/edit'),
            'view' => Pages\ViewDetalleNomina::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
