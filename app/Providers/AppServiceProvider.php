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
        if (config('app.env') === 'production' || config('app.url') !== 'http://localhost') {
            URL::forceScheme('https');
        }

        // Registrar el observer para DetalleNominas
        \App\Models\DetalleNominas::observe(\App\Observers\DetalleNominasObserver::class);

        // Registrar alias para Excel
        if (class_exists('Maatwebsite\\Excel\\ExcelServiceProvider')) {
            $this->app->alias('Excel', 'Maatwebsite\\Excel\\Facades\\Excel');
        }
    }
}
