<?php

namespace App\Filament\Resources\DoctorInventoryResource\Pages;

use App\Filament\Resources\DoctorInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorInventory extends EditRecord
{
    protected static string $resource = DoctorInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
