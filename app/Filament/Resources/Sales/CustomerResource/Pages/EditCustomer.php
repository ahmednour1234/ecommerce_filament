<?php

namespace App\Filament\Resources\Sales\CustomerResource\Pages;

use App\Filament\Resources\Sales\CustomerResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditCustomer extends BaseEditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

