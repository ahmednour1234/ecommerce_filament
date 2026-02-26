<?php

namespace App\Filament\Resources\MainCore\PaymentProviderResource\Pages;

use App\Filament\Resources\MainCore\PaymentProviderResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditPaymentProvider extends BaseEditRecord
{
    protected static string $resource = PaymentProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

