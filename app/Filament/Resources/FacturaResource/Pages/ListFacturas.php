<?php

namespace App\Filament\Resources\FacturaResource\Pages;

use App\Filament\Resources\FacturaResource;
use App\Filament\Resources\FacturaResource\Widgets\AperturaCajaPlaceholderWidget;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Session;

class ListFacturas extends ListRecords
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AperturaCajaPlaceholderWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function isTableVisible(): bool
    {
        return Session::has('apertura_id');
    }
}
