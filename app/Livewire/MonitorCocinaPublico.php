<?php

namespace App\Livewire;

use App\Filament\Pages\MonitorCocina;
use App\Models\OrdenRestaurante;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Versión PÚBLICA (sin login) del Monitor de Cocina.
 *
 * Las pantallas de cocina no tienen mouse ni teclado y solo muestran pedidos,
 * por lo que no requieren autenticación. Reutiliza el mismo mapa de secciones y
 * la misma consulta de órdenes que la página de Filament App\Filament\Pages\MonitorCocina,
 * pero se sirve por rutas propias fuera del panel /admin.
 */
#[Layout('components.layouts.monitor')]
class MonitorCocinaPublico extends Component
{
    // Clave interna de sección: 'general', 'china' o 'pizza'.
    public string $seccion = 'general';

    public function mount(?string $seccion = null): void
    {
        if ($seccion === null) {
            $this->seccion = 'general';

            return;
        }

        abort_unless(isset(MonitorCocina::SECCIONES[$seccion]), 404);

        $this->seccion = MonitorCocina::SECCIONES[$seccion]['key'];
    }

    /**
     * Pestañas de sección: [url, label, active] apuntando a las rutas públicas.
     */
    public function getSeccionTabsProperty(): array
    {
        return collect(MonitorCocina::SECCIONES)
            ->map(fn (array $seccion, string $slug) => [
                'url'    => url('/cocina/' . $slug),
                'label'  => $seccion['label'],
                'active' => $this->seccion === $seccion['key'],
            ])
            ->values()
            ->all();
    }

    /**
     * Mismas órdenes que el monitor del panel: pendientes (sin entregar) que
     * tengan platillos de comida de esta sección.
     */
    public function getOrdenesProperty()
    {
        return OrdenRestaurante::with(['numerosCocina', 'detalles.platillo' => function ($query) {
                $query->where('seccion', $this->seccion)
                      ->where('tipo', 'comida');
            }])
            ->whereHas('detalles.platillo', function ($query) {
                $query->where('seccion', $this->seccion)
                      ->where('tipo', 'comida');
            })
            ->whereNull('entregado_at')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.monitor-cocina-publico');
    }
}
