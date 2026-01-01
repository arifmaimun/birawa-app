<?php

namespace App\Filament\Resources\VisitStatusResource\Pages;

use App\Filament\Resources\VisitStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitStatus extends EditRecord
{
    protected static string $resource = VisitStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
