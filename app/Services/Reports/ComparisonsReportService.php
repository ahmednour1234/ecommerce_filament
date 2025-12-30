<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Comparisons Report Service (Period A vs Period B)
 */
class ComparisonsReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->with('account');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        // Get period A data
        $periodAFrom = $this->filters->additional['period_a_from'] ?? $this->filters->fromDate;
        $periodATo = $this->filters->additional['period_a_to'] ?? null;
        
        // Get period B data
        $periodBFrom = $this->filters->additional['period_b_from'] ?? null;
        $periodBTo = $this->filters->additional['period_b_to'] ?? null;

        if (!$periodAFrom || !$periodBFrom) {
            throw new \InvalidArgumentException('Both periods must be specified for comparison report.');
        }

        // Period A
        $queryA = $this->buildQuery()
            ->whereDate('entry_date', '>=', $periodAFrom);
        if ($periodATo) {
            $queryA->whereDate('entry_date', '<=', $periodATo);
        }
        $this->applyBranch($queryA);
        $entriesA = $queryA->get();
        $totalA = $entriesA->sum(fn($e) => (float) $e->balance);

        // Period B
        $queryB = $this->buildQuery()
            ->whereDate('entry_date', '>=', $periodBFrom);
        if ($periodBTo) {
            $queryB->whereDate('entry_date', '<=', $periodBTo);
        }
        $this->applyBranch($queryB);
        $entriesB = $queryB->get();
        $totalB = $entriesB->sum(fn($e) => (float) $e->balance);

        $variance = $totalB - $totalA;
        $variancePercent = $totalA != 0 ? ($variance / abs($totalA) * 100) : 0;

        $rows = [
            [
                'period' => trans_dash('reports.comparisons.period_a', 'Period A'),
                'from_date' => $periodAFrom,
                'to_date' => $periodATo,
                'amount' => $totalA,
            ],
            [
                'period' => trans_dash('reports.comparisons.period_b', 'Period B'),
                'from_date' => $periodBFrom,
                'to_date' => $periodBTo,
                'amount' => $totalB,
            ],
            [
                'period' => trans_dash('reports.comparisons.variance', 'Variance'),
                'from_date' => '',
                'to_date' => '',
                'amount' => $variance,
            ],
            [
                'period' => trans_dash('reports.comparisons.variance_percent', 'Variance %'),
                'from_date' => '',
                'to_date' => '',
                'amount' => $variancePercent,
            ],
        ];

        $totals = new TotalsDTO([
            'total_amount' => $totalB,
        ]);

        $summary = [
            'period_a_total' => \App\Support\Money::format($totalA),
            'period_b_total' => \App\Support\Money::format($totalB),
            'variance' => \App\Support\Money::format($variance),
            'variance_percent' => number_format($variancePercent, 2) . '%',
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
        return trans_dash('reports.comparisons.title', 'Comparisons Report');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.comparisons.period', 'Period'),
            trans_dash('reports.comparisons.from_date', 'From Date'),
            trans_dash('reports.comparisons.to_date', 'To Date'),
            trans_dash('reports.comparisons.amount', 'Amount'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['period'],
                $row['from_date'],
                $row['to_date'],
                \App\Support\Money::format($row['amount']),
            ];
        }, $rows);
    }
}

