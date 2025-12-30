<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Financial Position / Cost Center Report Service
 */
class FinancialPositionReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->with(['account', 'branch', 'costCenter']);
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        if ($this->filters->toDate) {
            $query->whereDate('entry_date', '<=', $this->filters->toDate);
        }
        $this->applyBranch($query);
        $this->applyCostCenter($query);

        $entries = $query->get();

        // Group by branch/cost center
        $grouped = [];
        foreach ($entries as $entry) {
            $key = ($entry->branch_id ?? 'no-branch') . '-' . ($entry->cost_center_id ?? 'no-cc');
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'branch' => $entry->branch?->name ?? 'N/A',
                    'cost_center' => $entry->costCenter?->name ?? 'N/A',
                    'total_debit' => 0,
                    'total_credit' => 0,
                ];
            }
            
            $grouped[$key]['total_debit'] += (float) $entry->debit;
            $grouped[$key]['total_credit'] += (float) $entry->credit;
        }

        $rows = [];
        foreach ($grouped as $data) {
            $balance = $data['total_debit'] - $data['total_credit'];
            $rows[] = [
                'branch' => $data['branch'],
                'cost_center' => $data['cost_center'],
                'total_debit' => $data['total_debit'],
                'total_credit' => $data['total_credit'],
                'balance' => $balance,
            ];
        }

        $totals = new TotalsDTO([
            'total_debit' => array_sum(array_column($rows, 'total_debit')),
            'total_credit' => array_sum(array_column($rows, 'total_credit')),
        ]);

        $summary = [
            'total_debit' => \App\Support\Money::format($totals->totalDebit),
            'total_credit' => \App\Support\Money::format($totals->totalCredit),
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
        return trans_dash('reports.financial_position.title', 'Financial Position Report');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.financial_position.branch', 'Branch'),
            trans_dash('reports.financial_position.cost_center', 'Cost Center'),
            trans_dash('reports.financial_position.total_debit', 'Total Debit'),
            trans_dash('reports.financial_position.total_credit', 'Total Credit'),
            trans_dash('reports.financial_position.balance', 'Balance'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['branch'],
                $row['cost_center'],
                \App\Support\Money::format($row['total_debit']),
                \App\Support\Money::format($row['total_credit']),
                \App\Support\Money::format($row['balance']),
            ];
        }, $rows);
    }
}

