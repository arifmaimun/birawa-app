<?php

namespace App\Filament\Resources\DoctorServiceCatalogResource\Pages;

use App\Filament\Resources\DoctorServiceCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorServiceCatalog extends EditRecord
{
    protected static string $resource = DoctorServiceCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
