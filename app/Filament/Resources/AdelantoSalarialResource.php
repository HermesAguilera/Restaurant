<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdelantoSalarialResource\Pages;
use App\Models\AdelantoSalarial;
use App\Models\Empleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdelantoSalarialResource extends Resource
{
    protected static ?string $model = AdelantoSalarial::class;
    protected static ?string $navigationGroup = 'Nominas';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Adelanto salarial';
    protected static ?string $modelLabel = 'Adelanto salarial';
    protected static ?string $pluralModelLabel = 'Adelantos salariales';
    protected static string $policy = \App\Policies\AdelantoSalarialPolicy::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del adelanto')
                ->schema([
                    Forms\Components\Select::make('empleado_id')
                        ->label('Empleado')
                        ->relationship('empleado', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('monto')
                        ->label('Monto')
                        ->numeric()
                        ->prefix('L.')
                        ->minValue(1)
                        ->required(),
                    Forms\Components\DatePicker::make('fecha_solicitud')
                        ->label('Fecha de solicitud')
                        ->default(now())
                        ->required(),
                    Forms\Components\Textarea::make('motivo')
                        ->label('Motivo')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Hidden::make('estado')
                        ->default('pendiente'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empleado.nombre')
                    ->label('Empleado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('HNL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => $state === 'aplicado' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('nomina.descripcion')
                    ->label('Aplicado en nómina')
                    ->placeholder('Pendiente')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (AdelantoSalarial $record) => $record->estado === 'pendiente'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (AdelantoSalarial $record) => $record->estado === 'pendiente'),
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
            'index' => Pages\ListAdelantoSalarials::route('/'),
            'create' => Pages\CreateAdelantoSalarial::route('/create'),
            'edit' => Pages\EditAdelantoSalarial::route('/{record}/edit'),
            'view' => Pages\ViewAdelantoSalarial::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }
}
