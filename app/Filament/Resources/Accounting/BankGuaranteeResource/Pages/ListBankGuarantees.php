<?php

namespace App\Filament\Resources\Accounting\BankGuaranteeResource\Pages;

use App\Filament\Resources\Accounting\BankGuaranteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBankGuarantees extends ListRecords
{
    protected static string $resource = BankGuaranteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

