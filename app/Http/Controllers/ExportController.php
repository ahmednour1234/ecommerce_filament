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
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function incomeStatementPdf(Request $request)
    {
        $exportData = session('income_statement_pdf_export');
        
        if (!$exportData) {
            abort(404, 'Export data not found');
        }

        $data = new Collection($exportData['data']);
        $export = new PdfExport($data, $exportData['headers'], $exportData['title'], $exportData['metadata']);
        
        return $export->download($exportData['filename']);
    }

    public function testArabicPdf()
    {
        $testData = collect([
            ['التاريخ' => '2024-01-15', 'النوع' => 'دخل', 'المبلغ' => '1,000.00', 'الرصيد' => '5,000.00'],
            ['التاريخ' => '2024-01-16', 'النوع' => 'مصروف', 'المبلغ' => '-500.00', 'الرصيد' => '4,500.00'],
            ['التاريخ' => '2024-01-17', 'النوع' => 'دخل', 'المبلغ' => '2,000.00', 'الرصيد' => '6,500.00'],
        ]);

        $headers = ['التاريخ', 'النوع', 'المبلغ', 'الرصيد'];
        $title = 'تقرير كشف حساب الفرع - اختبار';
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => 'System Test',
        ];

        $export = new PdfExport($testData, $headers, $title, $metadata);
        
        return $export->download('test-arabic-' . date('Y-m-d-His') . '.pdf');
    }
}

