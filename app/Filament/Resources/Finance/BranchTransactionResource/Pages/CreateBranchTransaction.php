<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranchTransaction extends CreateRecord
{
    protected static string $resource = BranchTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
