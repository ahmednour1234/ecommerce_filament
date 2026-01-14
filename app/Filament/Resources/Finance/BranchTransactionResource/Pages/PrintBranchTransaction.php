<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Resources\Pages\ViewRecord;

class PrintBranchTransaction extends ViewRecord
{
    protected static string $resource = BranchTransactionResource::class;

    protected static string $view = 'filament.finance.branch-transactions.print';
}
