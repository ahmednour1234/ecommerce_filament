<?php

namespace App\Filament\Resources\Sales\InvoiceResource\Pages;

use App\Filament\Resources\Sales\InvoiceResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditInvoice extends BaseEditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

