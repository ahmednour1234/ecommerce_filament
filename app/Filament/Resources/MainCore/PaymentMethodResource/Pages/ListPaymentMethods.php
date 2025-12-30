<?php

namespace App\Filament\Resources\MainCore\PaymentMethodResource\Pages;

use App\Filament\Resources\MainCore\PaymentMethodResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethods extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

