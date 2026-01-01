<?php

namespace App\Filament\Resources\VisitStatusResource\Pages;

use App\Filament\Resources\VisitStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisitStatuses extends ListRecords
{
    protected static string $resource = VisitStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
