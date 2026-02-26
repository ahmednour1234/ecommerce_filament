<?php

namespace App\Filament\Resources\MainCore\PaymentMethodResource\Pages;

use App\Filament\Resources\MainCore\PaymentMethodResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditPaymentMethod extends BaseEditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

