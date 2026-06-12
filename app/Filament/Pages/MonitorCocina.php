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

    public $seccion = 'general'; // 'general', 'china', 'pizza'

    public function getOrdenesProperty()
    {
        $query = OrdenRestaurante::with(['detalles.platillo' => function($query) {
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
