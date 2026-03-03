<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateServiceTransfer extends BaseCreateRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
