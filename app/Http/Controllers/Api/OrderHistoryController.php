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
            'periodo' => 'required|in:diario,semanal,quincenal,mensual',
            'fecha' => 'nullable|date',
        ]);

        $orders = $this->orderHistoryService->applyPeriodFilter(
            \App\Models\OrdenRestaurante::query(),
            $request->query('periodo'),
            $request->query('fecha')
        )->get();

        return response()->json($orders);
    }
}
