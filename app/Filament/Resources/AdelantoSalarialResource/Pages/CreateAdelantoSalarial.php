<?php

namespace App\Filament\Resources\AdelantoSalarialResource\Pages;

use App\Filament\Resources\AdelantoSalarialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdelantoSalarial extends CreateRecord
{
    protected static string $resource = AdelantoSalarialResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['estado'] = 'pendiente';
        $data['fecha_solicitud'] = $data['fecha_solicitud'] ?? now()->toDateString();

        return $data;
    }
}
