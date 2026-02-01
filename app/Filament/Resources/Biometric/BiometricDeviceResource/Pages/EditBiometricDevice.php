<?php

namespace App\Filament\Resources\Biometric\BiometricDeviceResource\Pages;

use App\Filament\Resources\Biometric\BiometricDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBiometricDevice extends EditRecord
{
    protected static string $resource = BiometricDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
