<?php

use App\Http\Controllers\Api\OrderHistoryController;
use App\Http\Controllers\Api\KitchenOrderApiController;
use Illuminate\Support\Facades\Route;

// El grupo `api` es stateless: sin el grupo `web` no hay sesión que `auth` pueda leer.
// Los consumidores son fetch() same-origin desde el panel, así que la cookie ya viaja.
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/orders/history', [OrderHistoryController::class, 'index']);
    Route::get('/orders/latest-pending', [KitchenOrderApiController::class, 'getLatestPending']);
});

// Endpoint PÚBLICO (sin login) para la alerta sonora de los monitores de cocina.
// Solo expone el último pedido pendiente (id/cliente/mesa/hora); es el mismo dato
// que ya muestran las pantallas de cocina públicas en /cocina/*.
Route::get('/public/kitchen/latest-pending', [KitchenOrderApiController::class, 'getLatestPending']);

