<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Permission;

$patterns = [
    'comercial_%',
    'compras_%',
    'inventario_%',
    'movimientos_inventario_%',
    'ordenes_producciones_%'
];

echo "Checking for obsolete permissions...\n";
foreach ($patterns as $pattern) {
    $permissions = Permission::where('name', 'like', $pattern)->get();
    foreach ($permissions as $permission) {
        echo "Deleting orphan permission: {$permission->name}\n";
        $permission->delete();
    }
}
echo "Done.";
