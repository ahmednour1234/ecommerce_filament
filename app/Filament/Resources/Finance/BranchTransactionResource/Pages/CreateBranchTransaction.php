<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Services\Finance\BranchTransactionService;
use Filament\Resources\Pages\CreateRecord;

class CreateBranchTransaction extends CreateRecord
{
    protected static string $resource = BranchTransactionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return app(BranchTransactionService::class)->create($data);
    }
}
