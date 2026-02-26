<?php

namespace App\Filament\Resources\HR\PositionResource\Pages;

use App\Filament\Resources\HR\PositionResource;
use App\Filament\Pages\BaseCreateRecord;

class CreatePosition extends BaseCreateRecord
{
    protected static string $resource = PositionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

