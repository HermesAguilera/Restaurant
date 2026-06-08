<?php

namespace App\Filament\Resources\CaiResource\Pages;

use App\Filament\Resources\CaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCai extends CreateRecord
{
    protected static string $resource = CaiResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');   
    }
    protected function getSavedNotificationTitle(): string
    {
        return 'Cai Creado';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->hasRole('root')) {
            if (empty($data['empresa_id'])) {
                throw ValidationException::withMessages([
                    'empresa_id' => 'Seleccione una empresa.',
                ]);
            }
            return $data;
        }

        $empresaId = auth()->user()->empresa_id;
        if (!$empresaId) {
            throw ValidationException::withMessages([
                'empresa_id' => 'Tu usuario no tiene una empresa asignada.',
            ]);
        }

        $data['empresa_id'] = $empresaId;
        return $data;
    }

}
