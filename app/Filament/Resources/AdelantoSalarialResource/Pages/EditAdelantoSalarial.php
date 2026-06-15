<?php

namespace App\Filament\Resources\AdelantoSalarialResource\Pages;

use App\Filament\Resources\AdelantoSalarialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdelantoSalarial extends EditRecord
{
    protected static string $resource = AdelantoSalarialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->estado === 'pendiente'),
        ];
    }
}
