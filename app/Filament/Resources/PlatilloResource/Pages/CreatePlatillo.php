<?php

namespace App\Filament\Resources\PlatilloResource\Pages;

use App\Filament\Resources\PlatilloResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlatillo extends CreateRecord
{
    protected static string $resource = PlatilloResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
