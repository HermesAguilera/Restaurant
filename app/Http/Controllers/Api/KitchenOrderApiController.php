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
     * Incluye `secciones`: las secciones de cocina del pedido (pizza, china,
     * general), para que el monitor reproduzca el sonido de cada una en
     * secuencia; un pedido con pizza y china suena con ambos.
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
                'secciones' => $this->seccionesDe($order),
                'created_at' => $order->created_at,
            ],
        ]);
    }

    /**
     * Secciones de cocina que abarca el pedido (solo comida), ordenadas
     * especialidad primero (pizza, china) y 'general' al final. El monitor
     * reproduce el sonido de cada una en secuencia.
     *
     * @return list<string>
     */
    private function seccionesDe(OrdenRestaurante $order): array
    {
        $secciones = $order->detalles()
            ->whereHas('platillo', fn ($q) => $q->where('tipo', 'comida'))
            ->with('platillo:id,seccion')
            ->get()
            ->pluck('platillo.seccion')
            ->filter()
            ->unique();

        $ordenadas = collect(['pizza', 'china', 'general'])
            ->filter(fn ($key) => $secciones->contains($key))
            ->values()
            ->all();

        return $ordenadas ?: ['general'];
    }
}
