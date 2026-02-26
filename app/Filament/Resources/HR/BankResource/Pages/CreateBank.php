<?php

namespace App\Filament\Resources\HR\BankResource\Pages;

use App\Filament\Resources\HR\BankResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateBank extends BaseCreateRecord
{
    protected static string $resource = BankResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

