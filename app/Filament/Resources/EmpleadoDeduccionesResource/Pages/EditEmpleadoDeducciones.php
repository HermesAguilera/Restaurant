<?php

namespace App\Filament\Resources\EmpleadoDeduccionesResource\Pages;

use App\Filament\Resources\EmpleadoDeduccionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;

class EditEmpleadoDeducciones extends EditRecord
{
    protected static string $resource = EmpleadoDeduccionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
        
    }
}
