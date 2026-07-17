<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\OrdenRestaurante;
use App\Models\User;

class KitchenApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica la API de pedidos pendientes.
     */
    public function test_api_returns_latest_pending_order(): void
    {
        $user = User::factory()->create();

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

        $response = $this->actingAs($user)->getJson('/api/orders/latest-pending');

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

    public function test_api_rechaza_peticiones_sin_autenticar(): void
    {
        $this->getJson('/api/orders/latest-pending')->assertUnauthorized();
        $this->getJson('/api/orders/history')->assertUnauthorized();
    }
}
