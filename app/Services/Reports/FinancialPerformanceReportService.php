<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Financial Performance Report Service (KPIs)
 */
class FinancialPerformanceReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['revenue', 'expense']);
            })
            ->with('account');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyFilters($query);
        
        $entries = $query->get();

        $revenue = 0;
        $expenses = 0;

        foreach ($entries as $entry) {
            if ($entry->account->type === 'revenue') {
                $revenue += (float) $entry->credit - (float) $entry->debit;
            } else {
                $expenses += (float) $entry->debit - (float) $entry->credit;
            }
        }

        $netIncome = $revenue - $expenses;
        $grossMargin = $revenue > 0 ? ($revenue - $expenses) / $revenue * 100 : 0;
        $netMargin = $revenue > 0 ? $netIncome / $revenue * 100 : 0;

        $rows = [
            [
                'kpi' => trans_dash('reports.financial_performance.revenue', 'Revenue'),
                'value' => $revenue,
                'percentage' => 100,
            ],
            [
                'kpi' => trans_dash('reports.financial_performance.expenses', 'Expenses'),
                'value' => $expenses,
                'percentage' => $revenue > 0 ? ($expenses / $revenue * 100) : 0,
            ],
            [
                'kpi' => trans_dash('reports.financial_performance.net_income', 'Net Income'),
                'value' => $netIncome,
                'percentage' => $netMargin,
            ],
            [
                'kpi' => trans_dash('reports.financial_performance.gross_margin', 'Gross Margin %'),
                'value' => $grossMargin,
                'percentage' => $grossMargin,
            ],
            [
                'kpi' => trans_dash('reports.financial_performance.net_margin', 'Net Margin %'),
                'value' => $netMargin,
                'percentage' => $netMargin,
            ],
        ];

        $totals = new TotalsDTO([
            'total_amount' => $netIncome,
        ]);

        $summary = [
            'revenue' => \App\Support\Money::format($revenue),
            'expenses' => \App\Support\Money::format($expenses),
            'net_income' => \App\Support\Money::format($netIncome),
            'gross_margin' => number_format($grossMargin, 2) . '%',
            'net_margin' => number_format($netMargin, 2) . '%',
        ];

        return new \App\Reports\DTOs\ReportDataDTO([
            'rows' => $rows,
            'totals' => $totals->toArray(),
            'summary' => $summary,
            'metadata' => $this->getMetadata(),
        ]);
    }

    protected function getReportTitle(): string
    {
        return trans_dash('reports.financial_performance.title', 'Financial Performance Report');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.financial_performance.kpi', 'KPI'),
            trans_dash('reports.financial_performance.value', 'Value'),
            trans_dash('reports.financial_performance.percentage', 'Percentage'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['kpi'],
                \App\Support\Money::format($row['value']),
                number_format($row['percentage'], 2) . '%',
            ];
        }, $rows);
    }
}

