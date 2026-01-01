<?php

namespace App\Filament\Resources\DoctorInventoryResource\Pages;

use App\Filament\Resources\DoctorInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctorInventories extends ListRecords
{
    protected static string $resource = DoctorInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
