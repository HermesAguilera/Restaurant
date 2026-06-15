<?php

namespace App\Filament\Resources\AdelantoSalarialResource\Pages;

use App\Filament\Resources\AdelantoSalarialResource;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewAdelantoSalarial extends ViewRecord
{
    protected static string $resource = AdelantoSalarialResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Detalle del adelanto')
                ->schema([
                    Placeholder::make('empleado')
                        ->label('Empleado')
                        ->content(fn () => $this->record->empleado?->nombre ?? 'N/A'),
                    Placeholder::make('monto')
                        ->label('Monto')
                        ->content(fn () => 'L. ' . number_format($this->record->monto ?? 0, 2)),
                    Placeholder::make('estado')
                        ->label('Estado')
                        ->content(fn () => ucfirst($this->record->estado ?? 'pendiente')),
                    Placeholder::make('fecha_solicitud')
                        ->label('Fecha de solicitud')
                        ->content(fn () => optional($this->record->fecha_solicitud)->format('d/m/Y') ?? 'N/A'),
                    Placeholder::make('fecha_aplicacion')
                        ->label('Fecha de aplicación')
                        ->content(fn () => optional($this->record->fecha_aplicacion)?->format('d/m/Y H:i') ?? 'Pendiente'),
                    Placeholder::make('motivo')
                        ->label('Motivo')
                        ->content(fn () => $this->record->motivo ?: 'N/A')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->estado === 'pendiente'),
        ];
    }
}
