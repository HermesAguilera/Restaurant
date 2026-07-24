<?php

namespace App\Http\Controllers\Api;

use App\Filament\Pages\MonitorCocina;
use App\Http\Controllers\Controller;
use App\Models\OrdenRestaurante;
use Illuminate\Http\JsonResponse;

class KitchenOrderApiController extends Controller
{
    /**
     * Devuelve el último pedido aún no entregado en formato JSON.
     *
     * Incluye `seccion`: la sección de cocina del pedido, para que el monitor
     * reproduzca el sonido propio de esa sección (pizza, china o general).
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
                'seccion' => $this->seccionDe($order),
                'created_at' => $order->created_at,
            ],
        ]);
    }

    /**
     * Sección de cocina "principal" del pedido: la primera (según el orden de
     * MonitorCocina::SECCIONES) que tenga platillos de comida. Un pedido puede
     * abarcar varias secciones, pero el parlante único suena una sola vez.
     */
    private function seccionDe(OrdenRestaurante $order): string
    {
        $secciones = $order->detalles()
            ->whereHas('platillo', fn ($q) => $q->where('tipo', 'comida'))
            ->with('platillo:id,seccion')
            ->get()
            ->pluck('platillo.seccion')
            ->filter()
            ->unique();

        return collect(MonitorCocina::SECCIONES)->pluck('key')
            ->first(fn ($key) => $secciones->contains($key)) ?? 'general';
    }
}
