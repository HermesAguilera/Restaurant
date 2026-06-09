<?php

namespace App\Filament\Resources\PlatilloResource\Pages;

use App\Filament\Resources\PlatilloResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatillo extends EditRecord
{
    protected static string $resource = PlatilloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
