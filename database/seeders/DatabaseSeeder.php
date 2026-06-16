<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crea todos los permisos del sistema
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $this->command->info('Configurando el usuario Root...');

        // 2. Crear rol root y darle todos los permisos
        $roleRoot = Role::firstOrCreate(
            ['name' => 'root'],
            ['guard_name' => 'web']
        );
        $roleRoot->syncPermissions(Permission::all());

        // 3. Crear el usuario root
        $user = User::firstOrCreate(
            ['email' => 'root@example.com'],
            [
                'name'     => 'Administrador Root',
                'password' => bcrypt('password'),
            ]
        );

        // 4. Asignar rol root al usuario
        $user->syncRoles([$roleRoot]);

        $this->command->info('Usuario Root configurado y enlazado correctamente.');
    }
}
