<?php

namespace App\Filament\Resources\ConsentTemplateResource\Pages;

use App\Filament\Resources\ConsentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsentTemplate extends EditRecord
{
    protected static string $resource = ConsentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
