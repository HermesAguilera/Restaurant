<?php

namespace App\Filament\Resources\EmpleadoResource\Pages;

use App\Filament\Resources\EmpleadoResource;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmpleado extends ViewRecord
{
    protected static string $resource = EmpleadoResource::class;

    public function form(Form $form): Form
    {
        return $form->schema($this->getFormSchema())->columns(2);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Datos del empleado')
                ->schema([
                    Placeholder::make('numero_empleado')->label('Número')->content(fn () => $this->record->numero_empleado ?? 'N/A'),
                    Placeholder::make('nombre')->label('Nombre')->content(fn () => $this->record->nombre ?? 'N/A'),
                    Placeholder::make('fecha_ingreso')->label('Fecha de ingreso')->content(fn () => optional($this->record->fecha_ingreso)->format('d/m/Y') ?? 'N/A'),
                    Placeholder::make('salario')->label('Salario')->content(fn () => number_format($this->record->salario ?? 0, 2)),
                    Placeholder::make('tipo_empleado')->label('Tipo de empleado')->content(fn () => $this->record->tipoEmpleado?->nombre_tipo ?? 'N/A'),
                ])
                ->columns(2)
                ->collapsible(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
