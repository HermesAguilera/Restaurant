<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\OrdenRestaurante;

class MonitorCocina extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $title = 'Monitor de Cocina';
    protected static ?string $navigationLabel = 'Monitor de Cocina';
    protected static ?string $navigationGroup = 'Restaurante';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.monitor-cocina';

    // El slug se mantiene sin parámetros para que el nombre de la ruta siga siendo
    // "filament.admin.pages.monitor-cocina"; el parámetro se agrega solo al path.
    protected static ?string $slug = 'monitor-cocina';

    /**
     * Mapa de slug en la URL => clave interna de sección (columna platillos.seccion).
     * Las URLs quedan: /admin/monitor-cocina/comida-general, .../comida-china, .../pizza
     */
    public const SECCIONES = [
        'comida-general' => ['key' => 'general', 'label' => 'Comida General'],
        'comida-china'   => ['key' => 'china',   'label' => 'Comida China'],
        'pizza'          => ['key' => 'pizza',   'label' => 'Pizza'],
    ];

    public $seccion = 'general'; // 'general', 'china', 'pizza'

    // Ruta con parámetro opcional: sin él (ej. desde el menú lateral) se abre
    // la sección de comida general por defecto.
    public static function getRoutePath(): string
    {
        return '/monitor-cocina/{seccion?}';
    }

    public function mount(?string $seccion = null): void
    {
        if ($seccion === null) {
            $this->seccion = 'general';

            return;
        }

        abort_unless(isset(static::SECCIONES[$seccion]), 404);

        $this->seccion = static::SECCIONES[$seccion]['key'];
    }

    /**
     * Pestañas para la barra de secciones: [url, label, active] por cada sección.
     * Se usan en la vista para que los botones naveguen a la ruta de cada sección.
     */
    public function getSeccionTabsProperty(): array
    {
        return collect(static::SECCIONES)
            ->map(fn (array $seccion, string $slug) => [
                'url'    => static::getUrl(['seccion' => $slug]),
                'label'  => $seccion['label'],
                'active' => $this->seccion === $seccion['key'],
            ])
            ->values()
            ->all();
    }

    // Ocultamos el encabezado de la página para aprovechar el espacio vertical.
    // El nombre del módulo ya aparece en la navegación lateral.
    public function getHeading(): string
    {
        return '';
    }

    public function getOrdenesProperty()
    {
        $query = OrdenRestaurante::with(['numerosCocina', 'detalles.platillo' => function($query) {
                $query->where('seccion', $this->seccion)
                      ->where('tipo', 'comida'); // Exclude bebidas
            }])
            ->whereHas('detalles.platillo', function($query) {
                $query->where('seccion', $this->seccion)
                      ->where('tipo', 'comida'); // Exclude bebidas
            })
            ->whereNull('entregado_at')
            ->orderBy('created_at', 'asc');

        return $query->get();
    }
}
