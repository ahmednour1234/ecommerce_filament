<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Models\Finance\BranchTransaction;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PrintBranchTransaction extends Page
{
    use InteractsWithRecord;

    protected static string $resource = BranchTransactionResource::class;

    protected static string $view = 'filament.finance.branch-transactions.print';

    public function mount(BranchTransaction $record): void
    {
        abort_unless(auth()->user()?->can('branch_tx.print'), 403);

        $this->record = $record->load([
            'branch', 'country', 'currency', 'creator', 'approver', 'rejecter',
        ]);
    }
}
