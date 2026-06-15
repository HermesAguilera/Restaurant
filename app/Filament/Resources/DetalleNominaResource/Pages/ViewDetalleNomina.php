<?php

namespace App\Filament\Resources\DetalleNominaResource\Pages;

use App\Filament\Resources\DetalleNominaResource;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewDetalleNomina extends ViewRecord
{
    protected static string $resource = DetalleNominaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function resolveRecord(int | string $key): Model
    {
        return parent::resolveRecord($key)->load('empleado');
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Datos del empleado')
                ->icon('heroicon-o-user')
                ->schema([
                    Placeholder::make('numero_empleado')->label('Número de empleado')->content(fn () => $this->record->empleado?->numero_empleado ?? 'N/A'),
                    Placeholder::make('nombre')->label('Nombre')->content(fn () => $this->record->empleado?->nombre ?? 'N/A'),
                    Placeholder::make('tipo_empleado')->label('Tipo de empleado')->content(fn () => $this->record->empleado?->tipoEmpleado?->nombre_tipo ?? 'N/A'),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Detalle de nómina')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Placeholder::make('sueldo')->label('Sueldo')->content(fn () => 'L. ' . number_format($this->record->sueldo_bruto ?? 0, 2)),
                    Placeholder::make('percepciones')->label('Percepciones')->content(fn () => 'L. ' . number_format($this->record->percepciones ?? 0, 2)),
                    Placeholder::make('deducciones')->label('Deducciones')->content(fn () => 'L. ' . number_format($this->record->deducciones ?? 0, 2)),
                    Placeholder::make('adelanto_salarial')->label('Adelanto salarial')->content(fn () => 'L. ' . number_format($this->record->adelanto_salarial ?? 0, 2)),
                    Placeholder::make('sueldo_neto')->label('Sueldo Neto')->content(fn () => 'L. ' . number_format($this->record->sueldo_neto ?? 0, 2)),
                ])
                ->columns(2)
                ->collapsible(),
        ];
    }
}
