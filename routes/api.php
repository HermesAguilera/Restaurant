<?php

use App\Http\Controllers\Api\OrderHistoryController;
use App\Http\Controllers\Api\KitchenOrderApiController;
use Illuminate\Support\Facades\Route;

Route::get('/orders/history', [OrderHistoryController::class, 'index']);
Route::get('/orders/latest-pending', [KitchenOrderApiController::class, 'getLatestPending']);

