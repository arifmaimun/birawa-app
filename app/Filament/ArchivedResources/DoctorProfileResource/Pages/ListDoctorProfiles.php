<?php

namespace App\Filament\Resources\DoctorProfileResource\Pages;

use App\Filament\Resources\DoctorProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctorProfiles extends ListRecords
{
    protected static string $resource = DoctorProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
