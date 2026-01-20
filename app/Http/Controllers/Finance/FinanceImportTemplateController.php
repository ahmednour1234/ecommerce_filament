<?php

namespace App\Http\Controllers\Finance;

use App\Exports\FinanceImportTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinanceImportTemplateController
{
    public function download(Request $request)
    {
        $user = auth()->user();
        abort_unless(
            $user?->hasRole('super_admin') || $user?->can('finance.transactions.import'),
            403
        );

        $kind = $request->get('kind', 'expense');
        
        if (!in_array($kind, ['income', 'expense'])) {
            $kind = 'expense';
        }

        $export = new FinanceImportTemplateExport($kind);
        $filename = 'finance-import-' . ($kind === 'expense' ? 'expenses' : 'income') . '-template.xlsx';
        
        return Excel::download($export, $filename);
    }
}
