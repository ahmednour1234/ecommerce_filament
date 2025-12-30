<?php

namespace App\Filament\Resources\MainCore\PaymentTransactionResource\Pages;

use App\Filament\Resources\MainCore\PaymentTransactionResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTransactions extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

