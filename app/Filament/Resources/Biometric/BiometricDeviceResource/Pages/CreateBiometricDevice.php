<?php

namespace App\Filament\Resources\Biometric\BiometricDeviceResource\Pages;

use App\Filament\Resources\Biometric\BiometricDeviceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBiometricDevice extends CreateRecord
{
    protected static string $resource = BiometricDeviceResource::class;

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
