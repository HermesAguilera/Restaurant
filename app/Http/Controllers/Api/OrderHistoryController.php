<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderHistoryService;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    protected $orderHistoryService;

    public function __construct(OrderHistoryService $orderHistoryService)
    {
        $this->orderHistoryService = $orderHistoryService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date',
        ]);

        $orders = $this->orderHistoryService->applyDailyFilter(
            \App\Models\OrdenRestaurante::query(),
            $request->query('fecha')
        )->get();

        return response()->json($orders);
    }
}
