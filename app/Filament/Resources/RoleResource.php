<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $modelLabel = 'Rol';
    protected static ?string $pluralModelLabel = 'Roles';

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Configuraciones';

    // Registrar la Policy
    protected static string $policy = \App\Policies\RolePolicy::class;

    public static function getPermissionModules(): array
    {
        return [
            'ventas' => 'Ventas',
            'recursos_humanos' => 'Recursos Humanos',
            'configuraciones' => 'Configuraciones',
            'caja_pos' => 'Caja/POS (Dashboard)',
            'monitor_cocina' => 'Monitor de cocina',
            'nominas' => 'Nóminas',
        ];
    }

    public static function getPermissionActions(): array
    {
        return ['ver', 'crear', 'actualizar', 'eliminar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Rol')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->minLength(2)
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(function ($state, $set) {
                                // Prevenir la creación del rol 'root' por no-root
                                if ($state === 'root' && ! auth()->user()->hasRole('root')) {
                                    throw ValidationException::withMessages([
                                        'name' => 'No tiene permiso para crear un rol con este nombre.',
                                    ]);
                                }
                            })
                            ->required(),
                    ])
                    ->columns(1),

                Section::make('Permisos por Módulo')
                    ->description('Selecciona los permisos específicos para cada módulo del sistema')
                    ->schema(
                        self::getPermissionFieldsets()
                    )
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    protected static function getPermissionFieldsets(): array
    {
        $fieldsets = [];
        $modules = self::getPermissionModules();
        $actionLabels = [
            'ver' => 'Ver',
            'crear' => 'Crear',
            'actualizar' => 'Editar',
            'eliminar' => 'Eliminar',
        ];

        foreach ($modules as $moduleKey => $moduleLabel) {
            $fieldsets[] = Fieldset::make($moduleLabel)
                ->schema([
                    Grid::make(4)
                        ->schema([
                            Forms\Components\Checkbox::make("permission_ver_{$moduleKey}")
                                ->label($actionLabels['ver'])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) use ($moduleKey) {
                                    self::updatePermissionRelationship($state, "{$moduleKey}_ver", $set);
                                })
                                ->afterStateHydrated(function ($component, $state, $record) use ($moduleKey) {
                                    if ($record) {
                                        $component->state($record->permissions->contains('name', "{$moduleKey}_ver"));
                                    }
                                }),

                            Forms\Components\Checkbox::make("permission_crear_{$moduleKey}")
                                ->label($actionLabels['crear'])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) use ($moduleKey) {
                                    self::updatePermissionRelationship($state, "{$moduleKey}_crear", $set);
                                })
                                ->afterStateHydrated(function ($component, $state, $record) use ($moduleKey) {
                                    if ($record) {
                                        $component->state($record->permissions->contains('name', "{$moduleKey}_crear"));
                                    }
                                }),

                            Forms\Components\Checkbox::make("permission_actualizar_{$moduleKey}")
                                ->label($actionLabels['actualizar'])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) use ($moduleKey) {
                                    self::updatePermissionRelationship($state, "{$moduleKey}_actualizar", $set);
                                })
                                ->afterStateHydrated(function ($component, $state, $record) use ($moduleKey) {
                                    if ($record) {
                                        $component->state($record->permissions->contains('name', "{$moduleKey}_actualizar"));
                                    }
                                }),

                            Forms\Components\Checkbox::make("permission_eliminar_{$moduleKey}")
                                ->label($actionLabels['eliminar'])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) use ($moduleKey) {
                                    self::updatePermissionRelationship($state, "{$moduleKey}_eliminar", $set);
                                })
                                ->afterStateHydrated(function ($component, $state, $record) use ($moduleKey) {
                                    if ($record) {
                                        $component->state($record->permissions->contains('name', "{$moduleKey}_eliminar"));
                                    }
                                }),
                        ]),
                ])
                ->columns(1);
        }

        return $fieldsets;
    }

    protected static function updatePermissionRelationship($state, $permissionName, $set)
    {
        // Esta función ahora maneja los cambios en tiempo real de los checkboxes.
        // El guardado real ocurre en mutateFormDataBeforeSave / afterCreate.
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')
                    ->label('Nombre del Rol')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->visible(auth()->user()->hasRole('root')),
                TextColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d-M-Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('Ver'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('Borrar')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Rol eliminado con éxito')
                    ->color('danger')
                    ->before(function ($record) {
                        // Prevenir eliminación del rol root por no-root
                        if ($record->name === 'root' && ! auth()->user()->hasRole('root')) {
                            throw new \Exception('No tiene permiso para eliminar este rol.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Prevenir eliminación masiva del rol root por no-root
                            if (! auth()->user()->hasRole('root')) {
                                $hasRoot = $records->contains('name', 'root');

                                if ($hasRoot) {
                                    throw new \Exception('No tiene permiso para eliminar el rol root.');
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()->hasRole('root')) {
            $query->where('name', '!=', 'root');
        }

        return $query->orderByDesc('id');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
            'view' => Pages\ViewRole::route('/{record}'),
        ];
    }
}
