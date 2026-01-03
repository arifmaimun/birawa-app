<?php

namespace App\Filament\Resources\EngagementTaskResource\Pages;

use App\Filament\Resources\EngagementTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEngagementTask extends EditRecord
{
    protected static string $resource = EngagementTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
