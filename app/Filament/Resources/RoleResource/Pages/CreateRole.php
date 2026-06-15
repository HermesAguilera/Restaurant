<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissions = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $modules = array_keys(RoleResource::getPermissionModules());
        $actions = RoleResource::getPermissionActions();

        $permissions = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $checkboxKey = "permission_{$action}_{$module}";

                if (! empty($data[$checkboxKey])) {
                    $permissions[] = "{$module}_{$action}";
                }

                unset($data[$checkboxKey]);
            }
        }

        $this->permissions = $permissions;

        return $data;
    }

    protected function afterCreate(): void
    {
        if (empty($this->permissions)) {
            return;
        }

        foreach ($this->permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $this->record->syncPermissions($this->permissions);
        $this->permissions = [];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rol creado exitosamente';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
