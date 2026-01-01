<?php

namespace App\Filament\Resources\ConsentTemplateResource\Pages;

use App\Filament\Resources\ConsentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsentTemplates extends ListRecords
{
    protected static string $resource = ConsentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
