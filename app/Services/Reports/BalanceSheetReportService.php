<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Balance Sheet Report Service
 */
class BalanceSheetReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return GeneralLedgerEntry::query()
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['asset', 'liability', 'equity']);
            })
            ->with('account');
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        // Log filters for debugging
        logger()->info('BalanceSheetReport filters', [
            'to_date' => $this->filters->toDate,
            'from_date' => $this->filters->fromDate,
            'posted_only' => $this->filters->postedOnly,
            'branch_id' => $this->filters->branchId,
            'cost_center_id' => $this->filters->costCenterId,
        ]);

        $query = $this->buildQuery();
        
        // Balance sheet is as of a specific date (inclusive - include entire day)
        // Make toDate inclusive by using end of day
        if ($this->filters->toDate) {
            $toDateEnd = \Carbon\Carbon::parse($this->filters->toDate)->endOfDay();
            $query->where('entry_date', '<=', $toDateEnd);
        }
        
        // Note: GeneralLedgerEntry is only created when JournalEntry is posted (via PostingService),
        // so all GeneralLedgerEntry records are implicitly from posted entries.
        // If postedOnly is false, we would need to include JournalEntryLine data, but that's a larger change.
        // For now, we only show GeneralLedgerEntry data (posted entries only).
        // If the user wants to see unposted entries, they should set postedOnly to false,
        // but this would require querying JournalEntryLine instead, which is not implemented yet.
        
        $this->applyBranch($query);
        $this->applyCostCenter($query);

        // Log query for debugging
        logger()->info('BalanceSheetReport SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        // Get all entries up to the date, then get the latest balance per account
        // The balance field in GeneralLedgerEntry is cumulative, so we need the latest entry per account
        $entries = $query->get();
        
        logger()->info('BalanceSheetReport entries count', ['count' => $entries->count()]);

        // Group by account and get the latest balance (highest entry_date, then highest id)
        $accountBalances = [];
        foreach ($entries as $entry) {
            $account = $entry->account;
            if (!$account) {
                continue;
            }

            $accountId = $account->id;
            
            // Keep only the latest entry per account (by entry_date, then by id)
            if (!isset($accountBalances[$accountId])) {
                $accountBalances[$accountId] = [
                    'account' => $account,
                    'balance' => (float) $entry->balance,
                    'entry_date' => $entry->entry_date,
                    'entry_id' => $entry->id,
                ];
            } else {
                // Compare dates and IDs to get the latest
                $currentDate = $accountBalances[$accountId]['entry_date'];
                $currentId = $accountBalances[$accountId]['entry_id'];
                
                if ($entry->entry_date > $currentDate || 
                    ($entry->entry_date == $currentDate && $entry->id > $currentId)) {
                    $accountBalances[$accountId] = [
                        'account' => $account,
                        'balance' => (float) $entry->balance,
                        'entry_date' => $entry->entry_date,
                        'entry_id' => $entry->id,
                    ];
                }
            }
        }

        // Group by account type
        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($accountBalances as $accountId => $data) {
            $account = $data['account'];
            $balance = $data['balance'];

            if ($account->type === 'asset') {
                $assets[$accountId] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalAssets += $balance;
            } elseif ($account->type === 'liability') {
                $liabilities[$accountId] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalLiabilities += $balance;
            } elseif ($account->type === 'equity') {
                $equity[$accountId] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalEquity += $balance;
            }
        }
        
        logger()->info('BalanceSheetReport totals', [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
        ]);

        // Build rows
        $rows = [];

        // Assets section
        $rows[] = [
            'section' => 'header',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.assets', 'ASSETS'),
            'balance' => null,
        ];

        foreach ($assets as $data) {
            if (!$this->filters->includeZeroRows && $data['balance'] == 0) {
                continue;
            }

            $rows[] = [
                'section' => 'asset',
                'account_code' => $data['account']->code,
                'account_name' => $data['account']->name,
                'balance' => $data['balance'],
            ];
        }

        $rows[] = [
            'section' => 'total',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.total_assets', 'Total Assets'),
            'balance' => $totalAssets,
            '_is_total' => true,
        ];

        // Liabilities section
        $rows[] = [
            'section' => 'header',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.liabilities', 'LIABILITIES'),
            'balance' => null,
        ];

        foreach ($liabilities as $data) {
            if (!$this->filters->includeZeroRows && $data['balance'] == 0) {
                continue;
            }

            $rows[] = [
                'section' => 'liability',
                'account_code' => $data['account']->code,
                'account_name' => $data['account']->name,
                'balance' => $data['balance'],
            ];
        }

        $rows[] = [
            'section' => 'total',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.total_liabilities', 'Total Liabilities'),
            'balance' => $totalLiabilities,
            '_is_total' => true,
        ];

        // Equity section
        $rows[] = [
            'section' => 'header',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.equity', 'EQUITY'),
            'balance' => null,
        ];

        foreach ($equity as $data) {
            if (!$this->filters->includeZeroRows && $data['balance'] == 0) {
                continue;
            }

            $rows[] = [
                'section' => 'equity',
                'account_code' => $data['account']->code,
                'account_name' => $data['account']->name,
                'balance' => $data['balance'],
            ];
        }

        $rows[] = [
            'section' => 'total',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.total_equity', 'Total Equity'),
            'balance' => $totalEquity,
            '_is_total' => true,
        ];

        // Total Liabilities + Equity
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;
        $rows[] = [
            'section' => 'total',
            'account_code' => '',
            'account_name' => trans_dash('reports.balance_sheet.total_liabilities_equity', 'Total Liabilities & Equity'),
            'balance' => $totalLiabilitiesEquity,
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'total_amount' => $totalAssets,
        ]);

        $summary = [
            'total_assets' => \App\Support\Money::format($totalAssets),
            'total_liabilities' => \App\Support\Money::format($totalLiabilities),
            'total_equity' => \App\Support\Money::format($totalEquity),
            'total_liabilities_equity' => \App\Support\Money::format($totalLiabilitiesEquity),
            'is_balanced' => abs($totalAssets - $totalLiabilitiesEquity) < 0.01 
                ? trans_dash('reports.yes', 'Yes') 
                : trans_dash('reports.no', 'No'),
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
        $summary = $data->summary;
        
        $errors = [];
        $warnings = [];

        // Check if Assets = Liabilities + Equity
        $assets = (float) str_replace([',', ' '], '', $summary['total_assets'] ?? '0');
        $liabilitiesEquity = (float) str_replace([',', ' '], '', $summary['total_liabilities_equity'] ?? '0');
        
        if (abs($assets - $liabilitiesEquity) >= 0.01) {
            $errors[] = trans_dash('reports.balance_sheet.not_balanced', 'Balance sheet is not balanced. Assets do not equal Liabilities + Equity.');
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    protected function getReportTitle(): string
    {
        $title = trans_dash('reports.balance_sheet.title', 'Balance Sheet');
        
        if ($this->filters->toDate) {
            $title .= ' - ' . $this->filters->toDate;
        }
        
        return $title;
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.balance_sheet.account_code', 'Account Code'),
            trans_dash('reports.balance_sheet.account_name', 'Account Name'),
            trans_dash('reports.balance_sheet.balance', 'Balance'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['account_code'],
                $row['account_name'],
                $row['balance'] !== null ? \App\Support\Money::format($row['balance']) : '',
            ];
        }, $rows);
    }
}

