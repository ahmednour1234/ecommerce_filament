<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\JournalEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Journal Entries by Year Report Service
 */
class JournalEntriesByYearReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return JournalEntry::query()
            ->with(['journal', 'branch', 'costCenter', 'user']);
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyDateRange($query, 'entry_date');
        $this->applyBranch($query);
        $this->applyCostCenter($query);
        
        if ($this->filters->postedOnly) {
            $query->where('is_posted', true);
        }

        $entries = $query->with('lines')->get();

        // Group by year and month
        $grouped = [];
        foreach ($entries as $entry) {
            $year = $entry->entry_date->format('Y');
            $month = $entry->entry_date->format('m');
            $key = "{$year}-{$month}";
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'year' => $year,
                    'month' => $month,
                    'count' => 0,
                    'total_debit' => 0,
                    'total_credit' => 0,
                ];
            }
            
            $grouped[$key]['count']++;
            
            // Get totals from lines
            $lines = $entry->lines ?? collect();
            foreach ($lines as $line) {
                $grouped[$key]['total_debit'] += (float) $line->debit;
                $grouped[$key]['total_credit'] += (float) $line->credit;
            }
        }

        $rows = [];
        foreach ($grouped as $data) {
            $rows[] = [
                'year' => $data['year'],
                'month' => $data['month'],
                'month_name' => \Carbon\Carbon::create($data['year'], $data['month'])->format('F'),
                'entry_count' => $data['count'],
                'total_debit' => $data['total_debit'],
                'total_credit' => $data['total_credit'],
            ];
        }

        $totals = new TotalsDTO([
            'total_debit' => array_sum(array_column($rows, 'total_debit')),
            'total_credit' => array_sum(array_column($rows, 'total_credit')),
        ]);

        $summary = [
            'total_entries' => array_sum(array_column($rows, 'entry_count')),
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
        return trans_dash('reports.journal_entries_by_year.title', 'Journal Entries by Year');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.journal_entries_by_year.year', 'Year'),
            trans_dash('reports.journal_entries_by_year.month', 'Month'),
            trans_dash('reports.journal_entries_by_year.entry_count', 'Entry Count'),
            trans_dash('reports.journal_entries_by_year.total_debit', 'Total Debit'),
            trans_dash('reports.journal_entries_by_year.total_credit', 'Total Credit'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['year'],
                $row['month_name'],
                $row['entry_count'],
                \App\Support\Money::format($row['total_debit']),
                \App\Support\Money::format($row['total_credit']),
            ];
        }, $rows);
    }
}

