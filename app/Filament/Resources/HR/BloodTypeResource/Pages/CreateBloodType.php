<?php

namespace App\Filament\Resources\HR\BloodTypeResource\Pages;

use App\Filament\Resources\HR\BloodTypeResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateBloodType extends BaseCreateRecord
{
    protected static string $resource = BloodTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

