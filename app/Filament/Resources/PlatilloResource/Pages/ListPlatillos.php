<?php

namespace App\Filament\Resources\PlatilloResource\Pages;

use App\Filament\Resources\PlatilloResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlatillos extends ListRecords
{
    protected static string $resource = PlatilloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
