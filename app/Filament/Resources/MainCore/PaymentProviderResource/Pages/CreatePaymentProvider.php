<?php

namespace App\Filament\Resources\MainCore\PaymentProviderResource\Pages;

use App\Filament\Resources\MainCore\PaymentProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentProvider extends CreateRecord
{
    protected static string $resource = PaymentProviderResource::class;
}

