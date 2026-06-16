<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Los módulos aquí deben coincidir con RoleResource::getPermissionModules()
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $acciones = ['ver', 'crear', 'actualizar', 'eliminar'];

        // IMPORTANTE: Estos módulos deben ser idénticos a los definidos en
        // App\Filament\Resources\RoleResource::getPermissionModules()
        $modulos = [
            'ventas',
            'recursos_humanos',
            'configuraciones',
            'caja_pos',
            'monitor_cocina',
            'nominas',
        ];

        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate([
                    'name'       => "{$modulo}_{$accion}",
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
