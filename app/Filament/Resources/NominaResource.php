<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NominaResource\Pages;
use App\Models\Nominas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NominaResource extends Resource
{
    protected static ?string $model = Nominas::class;
    protected static ?string $navigationGroup = 'Nominas';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $modelLabel = 'Nómina';
    protected static ?string $pluralModelLabel = 'Nóminas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la nómina')
                ->schema([
                    Forms\Components\Select::make('mes')->label('Mes')->options([
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                    ])->required(),
                    Forms\Components\Select::make('tipo_pago')->label('Tipo de pago')->options([
                        'mensual' => 'Mensual',
                        'quincenal' => 'Quincenal',
                        'semanal' => 'Semanal',
                    ])->required()->default('mensual'),
                    Forms\Components\TextInput::make('año')->label('Año')->default(date('Y'))->disabled()->dehydrated(),
                    Forms\Components\TextInput::make('descripcion')->label('Descripción')->maxLength(255),
                    Forms\Components\Toggle::make('cerrada')->label('Nómina cerrada')->default(false),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->searchable(),
            Tables\Columns\TextColumn::make('año')->label('Año'),
            Tables\Columns\TextColumn::make('mes')->label('Mes')->formatStateUsing(fn ($state) => [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
            ][(int) $state] ?? $state),
            Tables\Columns\TextColumn::make('tipo_pago')->label('Tipo de pago'),
            Tables\Columns\IconColumn::make('cerrada')->label('Cerrada')->boolean(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make()->visible(fn (Nominas $record) => ! $record->cerrada),
            Tables\Actions\DeleteAction::make()->visible(fn (Nominas $record) => ! $record->cerrada),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNominas::route('/'),
            'create' => Pages\CreateNomina::route('/create'),
            'edit' => Pages\EditNomina::route('/{record}/edit'),
            'view' => Pages\ViewNomina::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }
}
