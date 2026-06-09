<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Platillo;
use App\Models\OrdenRestaurante;
use App\Models\OrdenRestauranteDetalle;

class CajaDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Caja / POS';
    protected static ?string $title = 'Caja y Pedidos';
    protected static ?string $navigationGroup = 'Restaurante';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.caja-dashboard';

    // Estado del carrito
    public array $carrito = [];
    public string $nombre_cliente = 'Consumidor Final';
    public string $notas = '';
    public string $filtro_categoria = '';

    public function mount(): void
    {
        // No se necesita nada al montar, los platillos se cargan dinámicamente
    }

    public function getPlatillosProperty()
    {
        $query = Platillo::where('disponible', true)->orderBy('categoria')->orderBy('nombre');

        if ($this->filtro_categoria) {
            $query->where('categoria', $this->filtro_categoria);
        }

        return $query->get();
    }

    public function getCategoriasProperty(): array
    {
        return Platillo::where('disponible', true)
            ->distinct()
            ->pluck('categoria')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    public function agregarItem(int $platilloId): void
    {
        $platillo = Platillo::find($platilloId);
        if (!$platillo) return;

        $key = (string) $platilloId;

        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
        } else {
            $this->carrito[$key] = [
                'id'       => $platillo->id,
                'nombre'   => $platillo->nombre,
                'precio'   => (float) $platillo->precio,
                'cantidad' => 1,
            ];
        }
    }

    public function incrementar(string $key): void
    {
        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
        }
    }

    public function decrementar(string $key): void
    {
        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']--;
            if ($this->carrito[$key]['cantidad'] <= 0) {
                unset($this->carrito[$key]);
            }
        }
    }

    public function remover(string $key): void
    {
        unset($this->carrito[$key]);
    }

    public function limpiarCarrito(): void
    {
        $this->carrito = [];
        $this->nombre_cliente = 'Consumidor Final';
        $this->notas = '';
    }

    public function getTotalProperty(): float
    {
        return collect($this->carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);
    }

    public function enviarACocina(): void
    {
        if (empty($this->carrito)) {
            \Filament\Notifications\Notification::make()
                ->title('El carrito está vacío')
                ->warning()
                ->send();
            return;
        }

        $orden = OrdenRestaurante::create([
            'nombre_cliente' => $this->nombre_cliente ?: 'Consumidor Final',
            'notas'          => $this->notas,
            'total'          => $this->total,
            'estado'         => 'pendiente',
        ]);

        foreach ($this->carrito as $item) {
            OrdenRestauranteDetalle::create([
                'orden_restaurante_id' => $orden->id,
                'platillo_id'          => $item['id'],
                'cantidad'             => $item['cantidad'],
                'precio_unitario'      => $item['precio'],
                'subtotal'             => $item['precio'] * $item['cantidad'],
            ]);
        }

        // Resetear
        $this->limpiarCarrito();

        \Filament\Notifications\Notification::make()
            ->title("✅ Orden #{$orden->id} enviada a cocina")
            ->success()
            ->duration(3000)
            ->send();
    }
}
