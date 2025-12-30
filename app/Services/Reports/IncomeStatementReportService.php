<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\FilterDTO;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Income Statement Report Service
 */
class IncomeStatementReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['revenue', 'expense']);
            })
            ->with('account')
            ->orderBy('entry_date');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyFilters($query);

        $entries = $query->get();

        // Group by account type and account
        $revenueAccounts = [];
        $expenseAccounts = [];
        $totalRevenue = 0;
        $totalExpenses = 0;

        foreach ($entries as $entry) {
            $account = $entry->account;
            if (!$account) {
                continue;
            }

            $accountId = $account->id;
            
            if ($account->type === 'revenue') {
                // Revenue: Credits increase, Debits decrease
                $amount = (float) $entry->credit - (float) $entry->debit;
                
                if (!isset($revenueAccounts[$accountId])) {
                    $revenueAccounts[$accountId] = [
                        'account' => $account,
                        'amount' => 0,
                    ];
                }
                
                $revenueAccounts[$accountId]['amount'] += $amount;
                $totalRevenue += $amount;
            } else {
                // Expenses: Debits increase, Credits decrease
                $amount = (float) $entry->debit - (float) $entry->credit;
                
                if (!isset($expenseAccounts[$accountId])) {
                    $expenseAccounts[$accountId] = [
                        'account' => $account,
                        'amount' => 0,
                    ];
                }
                
                $expenseAccounts[$accountId]['amount'] += $amount;
                $totalExpenses += $amount;
            }
        }

        // Build rows
        $rows = [];

        // Revenue section
        $rows[] = [
            'section' => 'header',
            'account_code' => '',
            'account_name' => trans_dash('reports.income_statement.revenue', 'REVENUE'),
            'amount' => null,
        ];

        foreach ($revenueAccounts as $data) {
            if (!$this->filters->includeZeroRows && $data['amount'] == 0) {
                continue;
            }

            $rows[] = [
                'section' => 'revenue',
                'account_code' => $data['account']->code,
                'account_name' => $data['account']->name,
                'amount' => $data['amount'],
            ];
        }

        $rows[] = [
            'section' => 'total',
            'account_code' => '',
            'account_name' => trans_dash('reports.income_statement.total_revenue', 'Total Revenue'),
            'amount' => $totalRevenue,
            '_is_total' => true,
        ];

        // Expenses section
        $rows[] = [
            'section' => 'header',
            'account_code' => '',
            'account_name' => trans_dash('reports.income_statement.expenses', 'EXPENSES'),
            'amount' => null,
        ];

        foreach ($expenseAccounts as $data) {
            if (!$this->filters->includeZeroRows && $data['amount'] == 0) {
                continue;
            }

            $rows[] = [
                'section' => 'expense',
                'account_code' => $data['account']->code,
                'account_name' => $data['account']->name,
                'amount' => $data['amount'],
            ];
        }

        $rows[] = [
            'section' => 'total',
            'account_code' => '',
            'account_name' => trans_dash('reports.income_statement.total_expenses', 'Total Expenses'),
            'amount' => $totalExpenses,
            '_is_total' => true,
        ];

        // Net Income
        $netIncome = $totalRevenue - $totalExpenses;
        $rows[] = [
            'section' => 'net',
            'account_code' => '',
            'account_name' => trans_dash('reports.income_statement.net_income', 'NET INCOME'),
            'amount' => $netIncome,
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'total_amount' => $netIncome,
        ]);

        $summary = [
            'total_revenue' => \App\Support\Money::format($totalRevenue),
            'total_expenses' => \App\Support\Money::format($totalExpenses),
            'net_income' => \App\Support\Money::format($netIncome),
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
        
        $errors = [];
        $warnings = [];

        // Income statement doesn't need to balance like trial balance
        // But we can check if revenue and expenses are reasonable
        
        return [
            'is_valid' => true,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    protected function getReportTitle(): string
    {
        $title = trans_dash('reports.income_statement.title', 'Income Statement');
        
        if ($this->filters->fromDate && $this->filters->toDate) {
            $title .= ' (' . $this->filters->fromDate . ' to ' . $this->filters->toDate . ')';
        }
        
        return $title;
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.income_statement.account_code', 'Account Code'),
            trans_dash('reports.income_statement.account_name', 'Account Name'),
            trans_dash('reports.income_statement.amount', 'Amount'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['account_code'],
                $row['account_name'],
                $row['amount'] !== null ? \App\Support\Money::format($row['amount']) : '',
            ];
        }, $rows);
    }
}

