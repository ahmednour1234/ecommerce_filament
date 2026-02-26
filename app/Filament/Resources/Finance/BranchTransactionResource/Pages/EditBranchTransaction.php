<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditBranchTransaction extends BaseEditRecord
{
    protected static string $resource = BranchTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
