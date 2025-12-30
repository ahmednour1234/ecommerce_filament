<?php

namespace App\Filament\Resources\MainCore\PaymentMethodResource\Pages;

use App\Filament\Resources\MainCore\PaymentMethodResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use ExportsResourceTable;

    class ListPaymentMethods extends ListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

