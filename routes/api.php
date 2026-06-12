<?php

use App\Http\Controllers\Api\OrderHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/orders/history', [OrderHistoryController::class, 'index']);
