<?php

namespace App\Filament\Resources\EmpleadoResource\Pages;

use App\Filament\Resources\EmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpleado extends EditRecord
{
    protected static string $resource = EmpleadoResource::class;



    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $empleado = $this->record;
        // Prellenar deducciones seleccionadas
        $data['deducciones'] = $empleado->deduccionesAplicadas()->pluck('deduccion_id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $empleado = $this->record;
        // Guardar deducciones seleccionadas en el campo deducciones_aplicables
        $data['deducciones_aplicables'] = $data['deducciones'] ?? [];
        unset($data['deducciones']);
        // Sincronizar deducciones seleccionadas con la relación deducciones del empleado
        if (isset($data['deducciones_aplicables'])) {
            $empleado->deducciones()->sync($data['deducciones_aplicables']);
        }
        return $data;
    }

    // Ya no se sincronizan registros en EmpleadoDeducciones automáticamente aquí

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
