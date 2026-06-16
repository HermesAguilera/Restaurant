<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatilloResource\Pages;
use App\Models\Platillo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatilloResource extends Resource
{
    protected static ?string $model = Platillo::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Platillos del Menú';
    protected static ?string $navigationGroup = 'Restaurante';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Platillo';
    protected static ?string $pluralModelLabel = 'Platillos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del Platillo')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('Ej: Pollo a la plancha')
                    ->columnSpanFull(),

                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'comida' => 'Comida',
                        'bebida' => 'Bebida',
                    ])
                    ->default('comida')
                    ->live()
                    ->required(),

                Forms\Components\Select::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'Entradas'      => 'Entradas',
                        'Sopas'         => 'Sopas',
                        'Platos Fuertes'=> 'Platos Fuertes',
                        'Postres'       => 'Postres',
                        'Bebidas'       => 'Bebidas',
                        'Extras'        => 'Extras',
                    ])
                    ->searchable()
                    ->required()
                    ->hidden(fn (Forms\Get $get) => $get('tipo') === 'bebida'),

                Forms\Components\Select::make('seccion')
                    ->label('Sección de Cocina')
                    ->options([
                        'general' => 'Comida General',
                        'china'   => 'Comida China',
                        'pizza'   => 'Pizza',
                    ])
                    ->default('general')
                    ->required()
                    ->hidden(fn (Forms\Get $get) => $get('tipo') === 'bebida'),

                Forms\Components\TextInput::make('precio')
                    ->label('Precio (L.)')
                    ->numeric()
                    ->prefix('L.')
                    ->required()
                    ->minValue(0),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción (opcional)')
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('disponible')
                    ->label('Disponible en el menú')
                    ->default(true)
                    ->columnSpanFull(),
            ])->columns(['default' => 1, 'md' => 2]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Entradas'       => 'info',
                        'Sopas'          => 'warning',
                        'Platos Fuertes' => 'success',
                        'Postres'        => 'danger',
                        'Bebidas'        => 'primary',
                        default          => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('seccion')
                    ->label('Sección')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'general' => 'gray',
                        'china'   => 'danger',
                        'pizza'   => 'warning',
                        default   => 'gray',
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('HNL')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('disponible')
                    ->label('Disponible'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'Entradas'      => 'Entradas',
                        'Sopas'         => 'Sopas',
                        'Platos Fuertes'=> 'Platos Fuertes',
                        'Postres'       => 'Postres',
                        'Bebidas'       => 'Bebidas',
                        'Extras'        => 'Extras',
                    ]),
                Tables\Filters\TernaryFilter::make('disponible')
                    ->label('Disponibilidad'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('categoria');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlatillos::route('/'),
            'create' => Pages\CreatePlatillo::route('/create'),
            'edit'   => Pages\EditPlatillo::route('/{record}/edit'),
        ];
    }
}
