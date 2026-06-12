<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleadoResource\Pages;
use App\Models\Empleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmpleadoResource extends Resource
{
    protected static ?string $model = Empleado::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Recursos Humanos';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del empleado')
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre completo')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\DatePicker::make('fecha_ingreso')
                        ->label('Fecha de ingreso')
                        ->required(),
                    Forms\Components\TextInput::make('salario')
                        ->label('Salario')
                        ->numeric()
                        ->required(),
                    Forms\Components\Select::make('tipo_empleado_id')
                        ->label('Tipo de empleado')
                        ->relationship('tipoEmpleado', 'nombre_tipo')
                        ->required(),
                    Forms\Components\CheckboxList::make('deducciones')
                        ->label('Deducciones aplicables')
                        ->options(\App\Models\Deducciones::pluck('deduccion', 'id'))
                        ->columns(2),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_empleado')->label('Número')->searchable(),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('fecha_ingreso')->date()->label('Ingreso'),
                Tables\Columns\TextColumn::make('salario')->money('HNL', true)->label('Salario'),
                Tables\Columns\TextColumn::make('tipoEmpleado.nombre_tipo')->label('Tipo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->url(fn ($record) => static::getUrl('view', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpleados::route('/'),
            'create' => Pages\CreateEmpleado::route('/create'),
            'edit' => Pages\EditEmpleado::route('/{record}/edit'),
            'view' => Pages\ViewEmpleado::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }
}
