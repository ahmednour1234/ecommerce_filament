<?php

namespace App\Filament\Resources\MainCore\PaymentTransactionResource\Pages;

use App\Filament\Resources\MainCore\PaymentTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentTransaction extends EditRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

