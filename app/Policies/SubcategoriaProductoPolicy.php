<?php

namespace App\Policies;

use App\Models\SubcategoriaProducto;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubcategoriaProductoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('inventario_ver'); 
    }

    public function view(User $user, SubcategoriaProducto $subcategoriaProducto): bool
    {
        return $user->hasPermissionTo('inventario_ver'); 
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('inventario_crear'); 
    }

    public function update(User $user, SubcategoriaProducto $subcategoriaProducto): bool
    {
        return $user->hasPermissionTo('inventario_actualizar');
    }

    public function delete(User $user, SubcategoriaProducto $subcategoriaProducto): bool
    {
        return $user->hasPermissionTo('inventario_eliminar');
    }

    public function restore(User $user, SubcategoriaProducto $subcategoriaProducto): bool
    {
        return $user->hasPermissionTo('inventario_actualizar');
    }

    public function forceDelete(User $user, SubcategoriaProducto $subcategoriaProducto): bool
    {
        return $user->hasPermissionTo('inventario_eliminar');
    }
}