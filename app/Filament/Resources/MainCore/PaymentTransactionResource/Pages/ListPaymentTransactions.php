<?php

namespace App\Filament\Resources\MainCore\PaymentTransactionResource\Pages;

use App\Filament\Resources\MainCore\PaymentTransactionResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use ExportsResourceTable;

    class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

