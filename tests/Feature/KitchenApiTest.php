<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\OrdenRestaurante;
use App\Models\OrdenRestauranteCocinaNumero;
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
            'numero_dia' => 1,
            'fecha_orden' => now()->toDateString(),
        ]);

        $latest = OrdenRestaurante::create([
            'nombre_cliente' => 'Cliente 2',
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

    public function test_public_endpoint_incluye_la_seccion_del_pedido(): void
    {
        $pizza = \App\Models\Platillo::create(['nombre' => 'Pepperoni', 'precio' => 20, 'seccion' => 'pizza', 'tipo' => 'comida']);

        $orden = OrdenRestaurante::create(['nombre_cliente' => 'A', 'fecha_orden' => now()->toDateString()]);
        $orden->detalles()->create(['platillo_id' => $pizza->id, 'cantidad' => 1, 'precio_unitario' => 20, 'subtotal' => 20]);

        $this->getJson('/api/public/kitchen/latest-pending')
            ->assertOk()
            ->assertJson(['has_pending' => true, 'order' => ['id' => $orden->id, 'seccion' => 'pizza']]);
    }

    public function test_api_rechaza_peticiones_sin_autenticar(): void
    {
        $this->getJson('/api/orders/latest-pending')->assertUnauthorized();
        $this->getJson('/api/orders/history')->assertUnauthorized();
    }

    public function test_kitchen_order_numbers_are_independent_for_each_section(): void
    {
        $fecha = now()->toDateString();

        $primeraOrden = OrdenRestaurante::create([
            'nombre_cliente' => 'Cliente 1',
            'fecha_orden' => $fecha,
        ]);
        $segundaOrden = OrdenRestaurante::create([
            'nombre_cliente' => 'Cliente 2',
            'fecha_orden' => $fecha,
        ]);

        OrdenRestauranteCocinaNumero::create([
            'orden_restaurante_id' => $primeraOrden->id,
            'seccion' => 'general',
            'fecha_orden' => $fecha,
            'numero' => 1,
        ]);
        OrdenRestauranteCocinaNumero::create([
            'orden_restaurante_id' => $primeraOrden->id,
            'seccion' => 'china',
            'fecha_orden' => $fecha,
            'numero' => 1,
        ]);
        OrdenRestauranteCocinaNumero::create([
            'orden_restaurante_id' => $segundaOrden->id,
            'seccion' => 'general',
            'fecha_orden' => $fecha,
            'numero' => 2,
        ]);

        $primeraOrden->load('numerosCocina');
        $segundaOrden->load('numerosCocina');

        $this->assertSame(1, $primeraOrden->numeroCocinaPara('general'));
        $this->assertSame(1, $primeraOrden->numeroCocinaPara('china'));
        $this->assertSame(2, $segundaOrden->numeroCocinaPara('general'));
    }
}
