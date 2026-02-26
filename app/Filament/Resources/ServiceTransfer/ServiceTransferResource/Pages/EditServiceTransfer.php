<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditServiceTransfer extends BaseEditRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
