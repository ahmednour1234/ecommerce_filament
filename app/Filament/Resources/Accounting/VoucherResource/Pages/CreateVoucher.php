<?php

namespace App\Filament\Resources\Accounting\VoucherResource\Pages;

use App\Filament\Resources\Accounting\VoucherResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateVoucher extends BaseCreateRecord
{
    protected static string $resource = VoucherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}

