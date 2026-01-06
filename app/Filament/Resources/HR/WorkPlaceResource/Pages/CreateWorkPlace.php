<?php

namespace App\Filament\Resources\HR\WorkPlaceResource\Pages;

use App\Filament\Resources\HR\WorkPlaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkPlace extends CreateRecord
{
    protected static string $resource = WorkPlaceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

