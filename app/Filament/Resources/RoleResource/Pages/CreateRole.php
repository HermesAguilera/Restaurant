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
        // CRÍTICO: Asegurar que empresa_id esté presente para usuarios no-root
        if (!auth()->user()->hasRole('root')) {
            $data['empresa_id'] = auth()->user()->empresa_id;
        }
        
        // Si empresa_id no está presente y es requerido, asignar uno por defecto
        if (!isset($data['empresa_id'])) {
            $data['empresa_id'] = auth()->user()->empresa_id;
        }

        // Definir módulos y acciones directamente
        $modules = [
            'ventas',
            'recursos_humanos',  
            'configuraciones',
            'comercial',
            'inventario',
            'compras',
            'ordenes_producciones', 
            'nominas',   
        ];
        
        $actions = ['ver', 'crear', 'actualizar', 'eliminar'];
        
        // Extraer los permisos de los checkboxes
        $permissions = [];
        
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $checkboxKey = "permission_{$action}_{$module}";
                if (isset($data[$checkboxKey]) && $data[$checkboxKey]) {
                    $permissions[] = "{$module}_{$action}";
                }
                // Remover el checkbox del array de datos
                unset($data[$checkboxKey]);
            }
        }
        
        // Almacenar los permisos temporalmente
        $this->permissions = $permissions;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Crear permisos que no existan y asignar al rol
        if (!empty($this->permissions)) {
            // Crear permisos que no existan en la base de datos
            foreach ($this->permissions as $permissionName) {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }
            
            // Sincronizar los permisos con el rol
            $this->record->syncPermissions($this->permissions);
            
            // Limpiar la propiedad
            $this->permissions = [];
        }
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