<?php

namespace App\Filament\Resources\HR\BloodTypeResource\Pages;

use App\Filament\Resources\HR\BloodTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBloodType extends CreateRecord
{
    protected static string $resource = BloodTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

