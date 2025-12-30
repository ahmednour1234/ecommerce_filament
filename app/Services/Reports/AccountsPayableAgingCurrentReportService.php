<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Accounts Payable Aging (Current) Report Service
 * Note: Simplified implementation - enhance with actual supplier/AP tables
 */
class AccountsPayableAgingCurrentReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        // Stub - would use supplier/AP tables
        // Return an Eloquent builder instead of Query builder
        return \App\Models\Accounting\Account::query()->whereRaw('1 = 0');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        // Simplified: Return empty data structure
        // TODO: Implement with actual supplier/AP aging logic
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
        return trans_dash('reports.accounts_payable_aging_current.title', 'Accounts Payable Aging (Current)');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.accounts_payable_aging_current.supplier', 'Supplier'),
            trans_dash('reports.accounts_payable_aging_current.current', '0-30 Days'),
            trans_dash('reports.accounts_payable_aging_current.days_31_60', '31-60 Days'),
            trans_dash('reports.accounts_payable_aging_current.days_61_90', '61-90 Days'),
            trans_dash('reports.accounts_payable_aging_current.over_90', 'Over 90 Days'),
            trans_dash('reports.accounts_payable_aging_current.total', 'Total'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return $rows;
    }
}

