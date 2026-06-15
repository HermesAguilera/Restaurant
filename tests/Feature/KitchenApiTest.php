<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\OrdenRestaurante;

class KitchenApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica la API de pedidos pendientes.
     */
    public function test_api_returns_latest_pending_order(): void
    {
        // Crear ordenes
        OrdenRestaurante::create([
            'nombre_cliente' => 'Cliente 1',
            'estado' => 'pendiente',
            'numero_dia' => 1,
            'fecha_orden' => now()->toDateString(),
        ]);

        $latest = OrdenRestaurante::create([
            'nombre_cliente' => 'Cliente 2',
            'estado' => 'pendiente',
            'numero_dia' => 2,
            'fecha_orden' => now()->toDateString(),
        ]);

        $response = $this->getJson('/api/orders/latest-pending');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_pending' => true,
                'order' => [
                    'id' => $latest->id,
                    'nombre_cliente' => 'Cliente 2',
                ],
            ]);
    }
}
