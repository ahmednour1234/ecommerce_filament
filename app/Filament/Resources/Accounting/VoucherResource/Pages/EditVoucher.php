<?php

namespace App\Filament\Resources\Accounting\VoucherResource\Pages;

use App\Filament\Resources\Accounting\VoucherResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditVoucher extends BaseEditRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

