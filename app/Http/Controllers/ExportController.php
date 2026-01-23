<?php

namespace App\Http\Controllers;

use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\TrialBalanceReportService;
use App\Services\Reports\GeneralLedgerReportService;
use App\Services\Reports\IncomeStatementReportService;
use App\Exports\PdfExport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class ExportController extends Controller
{
    public function print(Request $request): View
    {
        $printData = session('print_data', [
            'title' => 'Report',
            'headers' => [],
            'rows' => [],
            'metadata' => [],
        ]);

        return view('exports.table-print', $printData);
    }

    public function reportPrint(Request $request, string $report): View
    {
        $filters = new FilterDTO($request->get('filters', []));
        
        $service = match($report) {
            'trial-balance' => new TrialBalanceReportService($filters),
            'general-ledger' => new GeneralLedgerReportService($filters),
            'income-statement' => new IncomeStatementReportService($filters),
            'account-statement' => new \App\Services\Reports\AccountStatementReportService($filters),
            'balance-sheet' => new \App\Services\Reports\BalanceSheetReportService($filters),
            'cash-flow' => new \App\Services\Reports\CashFlowReportService($filters),
            'vat' => new \App\Services\Reports\VatReportService($filters),
            'fixed-assets' => new \App\Services\Reports\FixedAssetsReportService($filters),
            'journal-entries-by-year' => new \App\Services\Reports\JournalEntriesByYearReportService($filters),
            'accounts-receivable' => new \App\Services\Reports\AccountsReceivableReportService($filters),
            'accounts-payable-aging-current' => new \App\Services\Reports\AccountsPayableAgingCurrentReportService($filters),
            'accounts-payable-aging-overdue' => new \App\Services\Reports\AccountsPayableAgingOverdueReportService($filters),
            'financial-position' => new \App\Services\Reports\FinancialPositionReportService($filters),
            'changes-in-equity' => new \App\Services\Reports\ChangesInEquityReportService($filters),
            'financial-performance' => new \App\Services\Reports\FinancialPerformanceReportService($filters),
            'comparisons' => new \App\Services\Reports\ComparisonsReportService($filters),
            default => throw new \InvalidArgumentException("Unknown report: {$report}"),
        };

        $printData = $service->getPrintViewData();
        $viewName = "reports.{$report}-print";

        return view($viewName, $printData);
    }

    public function branchStatementPdf(Request $request)
    {
        $exportData = session('branch_statement_pdf_export');
        
        if (!$exportData) {
            abort(404, 'Export data not found');
        }

        $data = new Collection($exportData['data']);
        $export = new PdfExport($data, $exportData['headers'], $exportData['title'], $exportData['metadata']);
        
        return $export->download($exportData['filename']);
    }
}

