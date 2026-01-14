<?php

namespace App\Http\Controllers\Finance;

use App\Models\Finance\BranchTransaction;

class BranchTransactionPrintController
{
    public function __invoke(BranchTransaction $branchTransaction)
    {
        abort_unless(auth()->user()?->can('branch_tx.print'), 403);

        $record = $branchTransaction->load(['branch','country','currency','creator','approver','rejecter']);

        return view('prints.branch-transaction', compact('record'));
    }
}
