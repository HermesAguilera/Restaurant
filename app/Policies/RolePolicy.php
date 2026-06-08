<?php

namespace App\Policies;


use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */

    public function viewAny(User $user): bool
    {
        return $user->can('configuraciones_ver');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('configuraciones_ver');
    }

    public function create(User $user): bool
    {
        return $user->can('configuraciones_crear');
    }

    public function update(User $user, Role $role): bool
    {   
        return $user->can('configuraciones_actualizar');
    }

    public function delete(User $user, Role $role): bool
    {   
        return $user->hasrole('root');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('root');
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->can('configuraciones_actualizar');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasrole('root');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasrole('root');
    }

}
