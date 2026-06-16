<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\RolePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\AdelantoSalarial::class => \App\Policies\AdelantoSalarialPolicy::class,
        // Mapear el modelo Role local (extiende SpatieRole) para que Filament lo encuentre
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // El usuario root tiene acceso total sin verificar permisos individuales.
        // Esto reemplaza de forma limpia y segura los overrides que estaban en User.php.
        Gate::before(function (User $user, string $ability): ?bool {
            // Temporalmente sin restricciones para ningún usuario, según lo solicitado.
            // (Todos actúan como super administradores).
            return true;
        });
    }
}
