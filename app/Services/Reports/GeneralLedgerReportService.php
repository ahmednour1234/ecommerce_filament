<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\FilterDTO;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * General Ledger Report Service
 */
class GeneralLedgerReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->with(['account', 'branch', 'costCenter', 'source'])
            ->orderBy('entry_date')
            ->orderBy('id');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyFilters($query);

        // Get opening balance if from_date is set
        $openingBalance = 0;
        if ($this->filters->fromDate && $this->filters->accountId) {
            $openingQuery = GeneralLedgerEntry::query()
                ->where('account_id', $this->filters->accountId)
                ->whereDate('entry_date', '<', $this->filters->fromDate);
            
            $this->applyBranch($openingQuery);
            $this->applyCostCenter($openingQuery);
            
            $openingEntries = $openingQuery->get();
            foreach ($openingEntries as $entry) {
                $openingBalance += (float) $entry->balance;
            }
        }

        $entries = $query->get();
        $rows = [];
        $runningBalance = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;

        // Add opening balance row if exists
        if ($openingBalance != 0) {
            $rows[] = [
                'date' => $this->filters->fromDate,
                'entry_number' => '',
                'reference' => trans_dash('reports.general_ledger.opening_balance', 'Opening Balance'),
                'description' => '',
                'debit' => $openingBalance > 0 ? abs($openingBalance) : 0,
                'credit' => $openingBalance < 0 ? abs($openingBalance) : 0,
                'balance' => $runningBalance,
                'branch' => '',
                'cost_center' => '',
            ];
        }

        foreach ($entries as $entry) {
            $debit = (float) $entry->debit;
            $credit = (float) $entry->credit;
            
            // Calculate running balance
            if ($entry->account) {
                if (in_array($entry->account->type, ['asset', 'expense'])) {
                    $runningBalance += $debit - $credit;
                } else {
                    $runningBalance += $credit - $debit;
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $rows[] = [
                'date' => $entry->entry_date->format('Y-m-d'),
                'entry_number' => $entry->source && method_exists($entry->source, 'entry_number') 
                    ? $entry->source->entry_number 
                    : ($entry->reference ?? ''),
                'reference' => $entry->reference ?? '',
                'description' => $entry->description ?? '',
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
                'branch' => $entry->branch?->name ?? '',
                'cost_center' => $entry->costCenter?->name ?? '',
            ];
        }

        $totals = new TotalsDTO([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'opening_balance' => $openingBalance,
            'closing_balance' => $runningBalance,
        ]);

        // Add totals row
        $rows[] = [
            'date' => '',
            'entry_number' => '',
            'reference' => 'TOTAL',
            'description' => '',
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'balance' => $runningBalance,
            'branch' => '',
            'cost_center' => '',
            '_is_total' => true,
        ];

        $summary = [
            'opening_balance' => \App\Support\Money::format($openingBalance),
            'total_debit' => \App\Support\Money::format($totalDebit),
            'total_credit' => \App\Support\Money::format($totalCredit),
            'closing_balance' => \App\Support\Money::format($runningBalance),
        ];

        return new \App\Reports\DTOs\ReportDataDTO([
            'rows' => $rows,
            'totals' => $totals->toArray(),
            'summary' => $summary,
            'metadata' => $this->getMetadata(),
        ]);
    }

    public function validateIntegrity(): array
    {
        $data = $this->getData();
        $totals = new TotalsDTO($data->totals);
        
        $errors = [];
        $warnings = [];

        // Check if debits equal credits
        if (!$totals->isBalanced()) {
            $errors[] = trans_dash('reports.general_ledger.not_balanced', 'General ledger is not balanced. Debits do not equal credits.');
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    protected function getReportTitle(): string
    {
        $title = trans_dash('reports.general_ledger.title', 'General Ledger');
        
        if ($this->filters->accountId) {
            $account = \App\Models\Accounting\Account::find($this->filters->accountId);
            if ($account) {
                $title .= ' - ' . $account->code . ' - ' . $account->name;
            }
        }
        
        if ($this->filters->fromDate && $this->filters->toDate) {
            $title .= ' (' . $this->filters->fromDate . ' to ' . $this->filters->toDate . ')';
        }
        
        return $title;
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.general_ledger.date', 'Date'),
            trans_dash('reports.general_ledger.entry_number', 'Entry #'),
            trans_dash('reports.general_ledger.reference', 'Reference'),
            trans_dash('reports.general_ledger.description', 'Description'),
            trans_dash('reports.general_ledger.debit', 'Debit'),
            trans_dash('reports.general_ledger.credit', 'Credit'),
            trans_dash('reports.general_ledger.balance', 'Balance'),
            trans_dash('reports.general_ledger.branch', 'Branch'),
            trans_dash('reports.general_ledger.cost_center', 'Cost Center'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['date'],
                $row['entry_number'],
                $row['reference'],
                $row['description'],
                \App\Support\Money::format($row['debit']),
                \App\Support\Money::format($row['credit']),
                \App\Support\Money::format($row['balance']),
                $row['branch'],
                $row['cost_center'],
            ];
        }, $rows);
    }
}

