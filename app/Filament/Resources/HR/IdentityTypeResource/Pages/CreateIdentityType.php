<?php

namespace App\Filament\Resources\HR\IdentityTypeResource\Pages;

use App\Filament\Resources\HR\IdentityTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIdentityType extends CreateRecord
{
    protected static string $resource = IdentityTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

