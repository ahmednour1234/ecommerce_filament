<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceTransfer extends ViewRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
