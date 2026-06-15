<?php

namespace App\Filament\Resources\AdelantoSalarialResource\Pages;

use App\Filament\Resources\AdelantoSalarialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdelantoSalarials extends ListRecords
{
    protected static string $resource = AdelantoSalarialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
