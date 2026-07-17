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
                            // El nombre "root" nunca se puede reasignar ni renombrar: el resto del
                            // sistema (User::canAccessPanel, Gate::before, etc.) depende de que ese
                            // string exista literalmente para conceder acceso total.
                            ->rule(fn (?Role $record) => function (string $attribute, $value, \Closure $fail) use ($record) {
                                if (strtolower($value) === 'root' && $record?->name !== 'root') {
                                    $fail('El nombre "root" está reservado y no se puede usar.');
                                }
                            })
                            ->disabled(fn (?Role $record): bool => $record?->name === 'root')
                            ->dehydrated(fn (?Role $record): bool => $record?->name !== 'root')
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
                        ->schema(
                            collect($actionLabels)->map(
                                fn (string $label, string $action) => Forms\Components\Checkbox::make("permission_{$action}_{$moduleKey}")
                                    ->label($label)
                                    ->afterStateHydrated(function ($component, $record) use ($moduleKey, $action) {
                                        if ($record) {
                                            $component->state($record->permissions->contains('name', "{$moduleKey}_{$action}"));
                                        }
                                    })
                            )->values()->all()
                        ),
                ])
                ->columns(1);
        }

        return $fieldsets;
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
                    ->visible(fn ($record): bool => $record->name !== 'root'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            if ($records->contains('name', 'root')) {
                                throw new \Exception('No se puede eliminar el rol root.');
                            }
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()?->hasRole('root')) {
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
