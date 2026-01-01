<?php

namespace App\Filament\Resources\DoctorProfileResource\Pages;

use App\Filament\Resources\DoctorProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctorProfile extends CreateRecord
{
    protected static string $resource = DoctorProfileResource::class;
}
