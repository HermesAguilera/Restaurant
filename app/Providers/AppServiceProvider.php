<?php

namespace App\Providers;

use App\Models\DetalleNominas;
use App\Observers\DetalleNominasObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL; // <-- Importamos la fachada URL

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
     }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS en producción (Esencial para proxies inversos como Render)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Registrar el observer para DetalleNominas
        \App\Models\DetalleNominas::observe(\App\Observers\DetalleNominasObserver::class);

        // Registrar alias para Excel
        if (class_exists('Maatwebsite\\Excel\\ExcelServiceProvider')) {
            $this->app->alias('Excel', 'Maatwebsite\\Excel\\Facades\\Excel');
        }

        // --- IMPORTANTE: Laravel 11 ya no carga AuthServiceProvider por defecto ---
        // Por lo tanto, el Gate::before debe ir aquí.
        \Illuminate\Support\Facades\Gate::before(function (\App\Models\User $user, string $ability): ?bool {
            // 1. Root y admins siempre tienen acceso total
            if ($user->email === 'admin@admin.com' || $user->email === 'root@example.com' || $user->hasRole('root')) {
                return true;
            }

            // 2. Mapeo de recursos de Filament hacia los módulos personalizados del cliente
            $moduleMapping = [
                // Ventas
                'platillo' => 'ventas',
                'caja' => 'ventas',
                'caja_apertura' => 'ventas',
                'factura' => 'ventas',
                'factura_caja' => 'ventas',
                'producto' => 'ventas',
                'productos' => 'ventas', // plural/singular fallback
                'proveedore' => 'ventas',
                'proveedores' => 'ventas',
                'categoria_producto' => 'ventas',
                'categoria_cliente_producto' => 'ventas',
                'subcategoria_producto' => 'ventas',
                'cai' => 'ventas',
                'historial_pedido' => 'ventas',
                'historial_pedidos' => 'ventas',

                // Recursos Humanos
                'empleado' => 'recursos_humanos',
                'tipo_empleado' => 'recursos_humanos',
                'empleado_deduccione' => 'recursos_humanos',
                'empleado_deducciones' => 'recursos_humanos',
                'empleado_percepcione' => 'recursos_humanos',
                'empleado_percepciones' => 'recursos_humanos',
                'empleado_persepcione' => 'recursos_humanos',
                'empleado_persepciones' => 'recursos_humanos',
                'deduccione' => 'recursos_humanos',
                'deducciones' => 'recursos_humanos',
                'percepcione' => 'recursos_humanos',
                'percepciones' => 'recursos_humanos',

                // Nóminas
                'adelanto_salarial' => 'nominas',
                'nomina' => 'nominas',
                'detalle_nomina' => 'nominas',

                // Configuraciones
                'user' => 'configuraciones',
                'role' => 'configuraciones',
                'permission' => 'configuraciones',

                // Dashboard (Caja / POS) y Monitor
                'dashboard' => 'caja_pos',
                'monitor_cocina' => 'monitor_cocina',
            ];

            // 3. Interceptar las validaciones de Filament (ej: view_any_platillo, create_user)
            foreach ($moduleMapping as $resource => $module) {
                // Verificamos si la habilidad termina en el nombre del recurso (ej: _platillo)
                // o si es exactamente el nombre del recurso
                if (str_ends_with($ability, '_' . $resource) || $ability === $resource) {
                    $action = 'ver'; // Por defecto, asumimos que ver es suficiente para listados (view_any, view)

                    if (str_starts_with($ability, 'create_') || $ability === 'create') {
                        $action = 'crear';
                    } elseif (str_starts_with($ability, 'update_') || str_starts_with($ability, 'edit_') || $ability === 'update') {
                        $action = 'actualizar';
                    } elseif (str_starts_with($ability, 'delete_') || str_starts_with($ability, 'force_delete_') || str_starts_with($ability, 'restore_') || $ability === 'delete') {
                        $action = 'eliminar';
                    }

                    // Construimos el nombre del permiso como lo guarda el RoleResource (ej: ventas_ver)
                    $customPermission = "{$module}_{$action}";

                    // Si el usuario tiene el permiso de ese módulo, autorizamos la acción
                    if ($user->hasPermissionTo($customPermission)) {
                        return true;
                    }
                }
            }

            // Dejamos que Filament/Spatie continúe con sus comprobaciones normales si no hubo coincidencia
            return null;
        });
    }
}
