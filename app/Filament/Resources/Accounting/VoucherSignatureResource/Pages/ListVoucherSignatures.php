<?php

namespace App\Filament\Resources\Accounting\VoucherSignatureResource\Pages;

use App\Filament\Resources\Accounting\VoucherSignatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVoucherSignatures extends ListRecords
{
    protected static string $resource = VoucherSignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

