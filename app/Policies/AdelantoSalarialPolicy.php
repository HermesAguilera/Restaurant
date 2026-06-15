<?php

namespace App\Policies;

use App\Models\AdelantoSalarial;
use App\Models\User;

class AdelantoSalarialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('nominas_ver');
    }

    public function view(User $user, AdelantoSalarial $adelantoSalarial): bool
    {
        return $user->can('nominas_ver');
    }

    public function create(User $user): bool
    {
        return $user->can('nominas_crear');
    }

    public function update(User $user, AdelantoSalarial $adelantoSalarial): bool
    {
        return $user->can('nominas_actualizar') && $adelantoSalarial->estado === 'pendiente';
    }

    public function delete(User $user, AdelantoSalarial $adelantoSalarial): bool
    {
        return $user->can('nominas_eliminar') && $adelantoSalarial->estado === 'pendiente';
    }
}
