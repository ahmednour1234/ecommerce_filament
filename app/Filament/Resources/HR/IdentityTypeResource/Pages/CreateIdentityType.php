<?php

namespace App\Filament\Resources\HR\IdentityTypeResource\Pages;

use App\Filament\Resources\HR\IdentityTypeResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateIdentityType extends BaseCreateRecord
{
    protected static string $resource = IdentityTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

