<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Accounts Payable Aging (Overdue) Report Service
 */
class AccountsPayableAgingOverdueReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return DB::table('accounts')->whereRaw('1 = 0');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $rows = [];
        $totals = new TotalsDTO();
        $summary = [];

        return new \App\Reports\DTOs\ReportDataDTO([
            'rows' => $rows,
            'totals' => $totals->toArray(),
            'summary' => $summary,
            'metadata' => $this->getMetadata(),
        ]);
    }

    protected function getReportTitle(): string
    {
        return trans_dash('reports.accounts_payable_aging_overdue.title', 'Accounts Payable Aging (Overdue)');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.accounts_payable_aging_overdue.supplier', 'Supplier'),
            trans_dash('reports.accounts_payable_aging_overdue.overdue_amount', 'Overdue Amount'),
            trans_dash('reports.accounts_payable_aging_overdue.days_overdue', 'Days Overdue'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return $rows;
    }
}

