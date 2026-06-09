<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\OrdenRestaurante;

class MonitorCocina extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $title = 'Monitor de Cocina (KDS)';
    protected static string $view = 'filament.pages.monitor-cocina';

    public function getOrdenesProperty()
    {
        return OrdenRestaurante::with('detalles.platillo')
            ->whereIn('estado', ['pendiente', 'en_cocina'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function marcarEnCocina($ordenId)
    {
        OrdenRestaurante::where('id', $ordenId)->update(['estado' => 'en_cocina']);
    }

    public function marcarListo($ordenId)
    {
        OrdenRestaurante::where('id', $ordenId)->update(['estado' => 'listo']);
        
        \Filament\Notifications\Notification::make()
            ->title('Orden Lista')
            ->success()
            ->send();
    }
}
