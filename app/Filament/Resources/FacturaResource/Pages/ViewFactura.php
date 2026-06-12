<?php

namespace App\Filament\Resources\FacturaResource\Pages;

use App\Filament\Resources\FacturaResource;
use App\Models\Factura;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewFactura extends ViewRecord
{
    protected static string $resource = FacturaResource::class;
    protected static string $view = 'filament.resources.factura-resource.pages.view-factura';

    protected function getHeaderActions(): array
    {
        $factura = $this->getRecord();

        return [
            Actions\Action::make('volver')
                ->label('Volver a la lista')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('primary'),

            Action::make('editar')
                ->label('Editar Factura')
                ->icon('heroicon-o-pencil')
                ->url(fn () => FacturaResource::getUrl('edit-pendiente', ['record' => $factura->id]))
                ->visible(fn () => $factura->estado === 'Pendiente'),

            Action::make('vista_previa')
                ->label('Imprimir Factura')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->url(fn () => route('facturas.visualizar', ['factura' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }

    protected function resolveRecord(int|string $key): Model
    {
        return Factura::with([
            'empleado.persona',
            'cai',
            'detalles.producto',
        ])->findOrFail($key);
    }
}
