<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Changes in Equity Report Service
 */
class ChangesInEquityReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->whereHas('account', fn($q) => $q->where('type', 'equity'))
            ->with('account');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        // Get opening equity
        $openingEquity = 0;
        if ($this->filters->fromDate) {
            $openingQuery = $this->buildQuery()
                ->whereDate('entry_date', '<', $this->filters->fromDate);
            $this->applyBranch($openingQuery);
            
            $openingEntries = $openingQuery->get();
            foreach ($openingEntries as $entry) {
                $openingEquity += (float) $entry->balance;
            }
        }

        $query = $this->buildQuery();
        $this->applyFilters($query);
        
        $entries = $query->get();

        $rows = [];
        $totalMovements = 0;

        foreach ($entries as $entry) {
            $amount = (float) $entry->balance;
            $totalMovements += $amount;
            
            $rows[] = [
                'date' => $entry->entry_date->format('Y-m-d'),
                'account_code' => $entry->account->code,
                'account_name' => $entry->account->name,
                'description' => $entry->description ?? '',
                'movement' => $amount,
            ];
        }

        $closingEquity = $openingEquity + $totalMovements;

        $rows[] = [
            'date' => '',
            'account_code' => '',
            'account_name' => trans_dash('reports.changes_in_equity.opening_equity', 'Opening Equity'),
            'description' => '',
            'movement' => $openingEquity,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'account_code' => '',
            'account_name' => trans_dash('reports.changes_in_equity.total_movements', 'Total Movements'),
            'description' => '',
            'movement' => $totalMovements,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'account_code' => '',
            'account_name' => trans_dash('reports.changes_in_equity.closing_equity', 'Closing Equity'),
            'description' => '',
            'movement' => $closingEquity,
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'opening_balance' => $openingEquity,
            'closing_balance' => $closingEquity,
        ]);

        $summary = [
            'opening_equity' => \App\Support\Money::format($openingEquity),
            'total_movements' => \App\Support\Money::format($totalMovements),
            'closing_equity' => \App\Support\Money::format($closingEquity),
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
        return trans_dash('reports.changes_in_equity.title', 'Changes in Equity');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.changes_in_equity.date', 'Date'),
            trans_dash('reports.changes_in_equity.account_code', 'Account Code'),
            trans_dash('reports.changes_in_equity.account_name', 'Account Name'),
            trans_dash('reports.changes_in_equity.description', 'Description'),
            trans_dash('reports.changes_in_equity.movement', 'Movement'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['date'],
                $row['account_code'],
                $row['account_name'],
                $row['description'],
                \App\Support\Money::format($row['movement']),
            ];
        }, $rows);
    }
}

