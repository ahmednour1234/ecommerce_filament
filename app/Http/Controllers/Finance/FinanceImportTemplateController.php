<?php

namespace App\Http\Controllers\Finance;

use App\Exports\FinanceImportTemplateExport;
use App\Models\MainCore\Branch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinanceImportTemplateController
{
    public function download(Request $request)
    {
        $kind = $request->get('kind', 'expense');

        if (!in_array($kind, ['income', 'expense'])) {
            $kind = 'expense';
        }

        $branchName = null;
        $branchId = $request->get('branch_id');
        if ($branchId) {
            $branch = Branch::find($branchId);
            $branchName = $branch?->name;
        }

        $filenameParts = ['finance-import', $kind === 'expense' ? 'expenses' : 'income'];
        if ($branchName) {
            $filenameParts[] = str_replace([' ', '/'], '-', $branchName);
        }
        $filenameParts[] = 'template';
        $filename = implode('-', $filenameParts) . '.xlsx';

        $export = new FinanceImportTemplateExport($kind, $branchName);

        return Excel::download($export, $filename);
    }
}
