<?php

namespace App\Filament\Resources\MainCore\PaymentTransactionResource\Pages;

use App\Filament\Resources\MainCore\PaymentTransactionResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditPaymentTransaction extends BaseEditRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

