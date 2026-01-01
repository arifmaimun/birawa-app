<?php

namespace App\Filament\Resources\VitalSignSettingResource\Pages;

use App\Filament\Resources\VitalSignSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVitalSignSettings extends ListRecords
{
    protected static string $resource = VitalSignSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
