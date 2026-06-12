<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CaiResource\Pages;
use App\Filament\Resources\CaiResource\RelationManagers;
use App\Models\Cai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;


class CaiResource extends Resource
{
    protected static ?string $model = Cai::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Icono más descriptivo para CAI
    protected static ?string $navigationGroup = 'Ventas'; // O el grupo de navegación que prefieras
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 1;
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('cai')
                            ->label('Código CAI')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            )
                            ->maxLength(255),

                        TextInput::make('establecimiento')
                            ->label('Establecimiento')
                            ->maxLength(3)
                            ->default('001')
                            ->required()
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            ),

                        TextInput::make('punto_emision')
                            ->label('Punto de Emisión')
                            ->maxLength(3)
                            ->default('001')
                            ->required()
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            ),

                        TextInput::make('tipo_documento')
                            ->label('Tipo Documento')
                            ->maxLength(2)
                            ->default('01')
                            ->required()
                            ->helperText('Ej: 01 para factura, 03 para nota de crédito')
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            ),

                        TextInput::make('rango_inicial')
                            ->label('Rango Inicial')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            ),

                        TextInput::make('rango_final')
                            ->label('Rango Final')
                            ->numeric()
                            ->required()
                            ->default(100)
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            ),


                        TextInput::make('numero_actual')
                            ->label('Número Actual')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->helperText('Actualizado automáticamente por el sistema.')
                            ->disabled(fn ($record, string $operation) => 
                                $operation === 'edit' && $record?->facturas()->exists()
                            ),


                        DatePicker::make('fecha_limite_emision')
                            ->label('Fecha Límite de Emisión')
                            ->required()
                            ->minDate(now()),

                        Toggle::make('activo')
                            ->label('Activo')
                            ->default(true), // Ocupa ambas columnas para mejor visualización
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('cai')
                    ->label('Código CAI')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rango_inicial')
                    ->label('Rango Inicial')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('rango_final')
                    ->label('Rango Final')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('numero_actual')
                    ->label('Número Actual')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('fecha_limite_emision')
                    ->label('Fecha Límite')
                    ->date('d/m/Y')
                    ->sortable(),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('activo')
                    ->options([
                        true => 'Activo',
                        false => 'Inactivo',
                    ])
                    ->label('Estado'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Agregamos la acción de ver
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Archivar') 
                    ->action(function (Cai $record) {
                        // 1. VERIFICACIÓN CRÍTICA: ¿Este CAI ya tiene facturas?
                        if ($record->facturas()->exists()) {
                            // SI TIENE FACTURAS: Bloquear la acción y notificar al usuario.
                            Notification::make()
                                ->danger()
                                ->title('Acción Denegada')
                                ->body('Este CAI no puede ser archivado porque tiene facturas históricas asociadas. Su registro es permanente por motivos fiscales.')
                                ->send();
                            
                            // Detener la ejecución para no borrar nada.
                            return; 
                        }

                        // NO TIENE FACTURAS: Proceder con el borrado lógico (soft delete).
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('CAI Archivado')
                            ->body('El CAI ha sido archivado correctamente ya que no tenía facturas emitidas.')
                            ->send();
                    }),// Agregamos la acción de eliminar
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Puedes añadir relation managers aquí si los necesitas
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCais::route('/'),
            'create' => Pages\CreateCai::route('/create'),
            'view' => Pages\ViewCai::route('/{record}'), 
            'edit' => Pages\EditCai::route('/{record}/edit'),
        ];
    }


    // Método para definir el query base para el recurso
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->orderBy('created_at', 'desc'); 
    }
}
