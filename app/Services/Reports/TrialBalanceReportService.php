<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\FilterDTO;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trial Balance Report Service
 */
class TrialBalanceReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        // Use GeneralLedgerEntry as base, but we'll aggregate by account
        return GeneralLedgerEntry::query()
            ->whereHas('account', fn($q) => $q->where('is_active', true));
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyFilters($query);

        // Get entries grouped by account
        $entries = $query->with('account')->get();

        // Aggregate by account
        $accounts = [];
        foreach ($entries as $entry) {
            $accountId = $entry->account_id;
            if (!isset($accounts[$accountId])) {
                $accounts[$accountId] = [
                    'account' => $entry->account,
                    'debits' => 0,
                    'credits' => 0,
                ];
            }

            $accounts[$accountId]['debits'] += (float) $entry->debit;
            $accounts[$accountId]['credits'] += (float) $entry->credit;
        }

        // Format rows
        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $accountId => $data) {
            $account = $data['account'];
            $debits = $data['debits'];
            $credits = $data['credits'];

            // Calculate balance based on account type
            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $debits - $credits;
            } else {
                $balance = $credits - $debits;
            }

            // Skip zero rows if filter says so
            if (!$this->filters->includeZeroRows && $debits == 0 && $credits == 0) {
                continue;
            }

            $totalDebit += $debits;
            $totalCredit += $credits;

            $rows[] = [
                'account_code' => $account->code,
                'account_name' => $account->name,
                'account_type' => $account->type,
                'debits' => $debits,
                'credits' => $credits,
                'balance' => $balance,
            ];
        }

        // Sort by account code
        usort($rows, fn($a, $b) => strcmp($a['account_code'], $b['account_code']));

        $totals = new TotalsDTO([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);

        // Add totals row
        $rows[] = [
            'account_code' => '',
            'account_name' => 'TOTAL',
            'account_type' => '',
            'debits' => $totalDebit,
            'credits' => $totalCredit,
            'balance' => $totals->getNetBalance(),
            '_is_total' => true,
        ];

        $summary = [
            'total_debit' => \App\Support\Money::format($totalDebit),
            'total_credit' => \App\Support\Money::format($totalCredit),
            'net_balance' => \App\Support\Money::format($totals->getNetBalance()),
            'is_balanced' => $totals->isBalanced() ? trans_dash('reports.yes', 'Yes') : trans_dash('reports.no', 'No'),
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
            $errors[] = trans_dash('reports.trial_balance.not_balanced', 'Trial balance is not balanced. Debits do not equal credits.');
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    protected function getReportTitle(): string
    {
        $asOfDate = $this->filters->toDate ?? now()->format('Y-m-d');
        return trans_dash('reports.trial_balance.title', 'Trial Balance') . ' - ' . $asOfDate;
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.trial_balance.account_code', 'Account Code'),
            trans_dash('reports.trial_balance.account_name', 'Account Name'),
            trans_dash('reports.trial_balance.account_type', 'Type'),
            trans_dash('reports.trial_balance.debits', 'Debits'),
            trans_dash('reports.trial_balance.credits', 'Credits'),
            trans_dash('reports.trial_balance.balance', 'Balance'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['account_code'],
                $row['account_name'],
                $row['account_type'],
                \App\Support\Money::format($row['debits']),
                \App\Support\Money::format($row['credits']),
                \App\Support\Money::format($row['balance']),
            ];
        }, $rows);
    }
}

