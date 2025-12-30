<?php

namespace App\Filament\Resources\MainCore\PaymentProviderResource\Pages;

use App\Filament\Resources\MainCore\PaymentProviderResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentProviders extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = PaymentProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

