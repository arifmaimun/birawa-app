<?php

namespace App\Filament\Resources\VitalSignSettingResource\Pages;

use App\Filament\Resources\VitalSignSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVitalSignSetting extends EditRecord
{
    protected static string $resource = VitalSignSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
