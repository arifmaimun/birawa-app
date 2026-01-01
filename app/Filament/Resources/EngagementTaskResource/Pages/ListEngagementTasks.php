<?php

namespace App\Filament\Resources\EngagementTaskResource\Pages;

use App\Filament\Resources\EngagementTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEngagementTasks extends ListRecords
{
    protected static string $resource = EngagementTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
