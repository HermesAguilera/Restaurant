<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $acciones = ['ver', 'crear', 'actualizar', 'eliminar'];

        $modulos = [
            'ventas',
            'recursos_humanos',
            'configuraciones',
            'comercial',
            'inventario',
            'compras',
            'ordenes_producciones',
            'caja_pos',
            'monitor_cocina',
            'nominas',
            'rendimientos',
            'movimientos_inventario',
        ];

        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate(['name' => "{$modulo}_{$accion}"]);
            }
        }
    }
}
