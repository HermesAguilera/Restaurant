<?php

namespace App\Filament\Resources\NominaResource\Pages;

use App\Filament\Resources\NominaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditNomina extends EditRecord
{
    protected static string $resource = NominaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn (Model $record) => ! $record->cerrada),
        ];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if ($this->getRecord()->cerrada) {
            $this->redirect(route('filament.admin.resources.nominas.view', $this->record));
        }
    }
}
