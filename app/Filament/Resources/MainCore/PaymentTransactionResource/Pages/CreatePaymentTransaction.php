<?php

namespace App\Filament\Resources\MainCore\PaymentTransactionResource\Pages;

use App\Filament\Resources\MainCore\PaymentTransactionResource;
use Filament\Actions;
use App\Filament\Pages\BaseCreateRecord;

class CreatePaymentTransaction extends BaseCreateRecord
{
    protected static string $resource = PaymentTransactionResource::class;
}

