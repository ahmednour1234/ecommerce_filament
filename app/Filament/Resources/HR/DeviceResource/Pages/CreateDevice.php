<?php

namespace App\Filament\Resources\HR\DeviceResource\Pages;

use App\Filament\Resources\HR\DeviceResource;
use App\Services\HR\DeviceService;
use Filament\Resources\Pages\CreateRecord;

class CreateDevice extends CreateRecord
{
    protected static string $resource = DeviceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['api_key'])) {
            $data['api_key'] = bin2hex(random_bytes(32));
        }
        return $data;
    }
}

