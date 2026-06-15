<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissions = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('volver')
                ->label('Volver a la lista')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('primary'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $modules = array_keys(RoleResource::getPermissionModules());
        $actions = RoleResource::getPermissionActions();

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionName = "{$module}_{$action}";
                $checkboxKey = "permission_{$action}_{$module}";

                $data[$checkboxKey] = $this->record->permissions->contains('name', $permissionName);
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $modules = array_keys(RoleResource::getPermissionModules());
        $actions = RoleResource::getPermissionActions();
        $selectedPermissions = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $checkboxKey = "permission_{$action}_{$module}";

                if (! empty($data[$checkboxKey])) {
                    $selectedPermissions[] = "{$module}_{$action}";
                }

                unset($data[$checkboxKey]);
            }
        }

        $this->permissions = $selectedPermissions;

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $this->record->syncPermissions($this->permissions);
        $this->permissions = [];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Rol actualizado exitosamente';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
