<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\OrdenRestaurante;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $ordenes = OrdenRestaurante::whereDate('fecha_orden', '<', now()->toDateString())->get();
    foreach ($ordenes as $orden) {
        $orden->detalles()->delete();
        $orden->delete();
    }
})->daily();
