<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceTransfer extends CreateRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
