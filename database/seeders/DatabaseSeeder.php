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
        $this->call([
            RolesAndPermissionsSeeder::class,
            // Agrega aquí otros seeders válidos si los necesitas, ej: ProductosSeeder::class
        ]);
        
        $this->command->info('Configurando el usuario Root...');

        // --- 1. Crear roles ---
        $roleRoot = Role::firstOrCreate(
            ['name' => 'root'],
            ['guard_name' => 'web']
        );
        $roleRoot->givePermissionTo(Permission::all()); // Asignar todos los permisos

        $roleAdmin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $roleAdmin->givePermissionTo(Permission::all());

        // --- 2. Crear el Usuario root ---
        $user = User::firstOrCreate(
            ['email' => 'root@example.com'],
            [
                'name' => 'Administrador Root',
                'password' => bcrypt('password'),
            ]
        );

        // --- 3. Asignar rol ---
        $user->assignRole($roleRoot);

        $this->command->info('Usuario Root configurado y enlazado correctamente.');
    }
}
