<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenRestaurante;
use Illuminate\Http\JsonResponse;

class KitchenOrderApiController extends Controller
{
    /**
     * Devuelve el último pedido aún no entregado en formato JSON.
     *
     * @return JsonResponse
     */
    public function getLatestPending(): JsonResponse
    {
        $order = OrdenRestaurante::whereNull('entregado_at')
            ->orderBy('id', 'desc')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => true,
                'has_pending' => false,
                'order' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'has_pending' => true,
            'order' => [
                'id' => $order->id,
                'nombre_cliente' => $order->nombre_cliente,
                'mesa' => $order->mesa,
                'created_at' => $order->created_at,
            ],
        ]);
    }
}
