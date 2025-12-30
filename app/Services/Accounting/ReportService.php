<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Services\Accounting\GeneralLedgerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get cached COA tree structure
     */
    public function getCachedCoaTree(): array
    {
        return Cache::remember('coa_tree', 3600, function () {
            $accounts = Account::with('children')->whereNull('parent_id')->orderBy('code')->get();
            return $this->buildTree($accounts);
        });
    }

    /**
     * Build account tree recursively
     */
    protected function buildTree($accounts): array
    {
        $tree = [];
        foreach ($accounts as $account) {
            $node = [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'children' => $this->buildTree($account->children),
            ];
            $tree[] = $node;
        }
        return $tree;
    }

    /**
     * Clear COA cache
     */
    public function clearCoaCache(): void
    {
        Cache::forget('coa_tree');
    }

    /**
     * Get account ledger with caching
     */
    public function getAccountLedger(int $accountId, ?\DateTime $fromDate = null, ?\DateTime $toDate = null, ?int $branchId = null, ?int $costCenterId = null): array
    {
        $cacheKey = "account_ledger_{$accountId}_" . 
            ($fromDate ? $fromDate->format('Y-m-d') : 'all') . '_' .
            ($toDate ? $toDate->format('Y-m-d') : 'all') . '_' .
            ($branchId ?? 'all') . '_' .
            ($costCenterId ?? 'all');

        return Cache::remember($cacheKey, 1800, function () use ($accountId, $fromDate, $toDate, $branchId, $costCenterId) {
            $query = JournalEntryLine::where('account_id', $accountId)
                ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                    $q->where('is_posted', true);
                    if ($fromDate) {
                        $q->whereDate('entry_date', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $q->whereDate('entry_date', '<=', $toDate);
                    }
                })
                ->with(['journalEntry', 'account', 'branch', 'costCenter']);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            if ($costCenterId) {
                $query->where('cost_center_id', $costCenterId);
            }

            return $query->orderBy('created_at')->get()->toArray();
        });
    }

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats(?int $branchId = null): array
    {
        $cacheKey = 'dashboard_stats_' . ($branchId ?? 'all');
        
        return Cache::remember($cacheKey, 300, function () use ($branchId) {
            $query = JournalEntry::where('is_posted', true);
            
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalEntries = $query->count();
            $totalAmount = $query->join('journal_entry_lines', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->sum(DB::raw('journal_entry_lines.debit + journal_entry_lines.credit'));

            return [
                'total_entries' => $totalEntries,
                'total_amount' => $totalAmount,
                'last_entry_date' => $query->max('entry_date'),
            ];
        });
    }

    /**
     * Clear dashboard stats cache
     */
    public function clearDashboardStatsCache(?int $branchId = null): void
    {
        $cacheKey = 'dashboard_stats_' . ($branchId ?? 'all');
        Cache::forget($cacheKey);
    }

    /**
     * Get trial balance with filters
     */
    public function getTrialBalance(array $filters = []): array
    {
        $fromDate = isset($filters['from_date']) ? \Carbon\Carbon::parse($filters['from_date']) : null;
        $toDate = isset($filters['to_date']) ? \Carbon\Carbon::parse($filters['to_date']) : now();
        
        $query = GeneralLedgerEntry::query()
            ->whereHas('account', fn($q) => $q->where('is_active', true));
        
        if ($fromDate) {
            $query->whereDate('entry_date', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('entry_date', '<=', $toDate);
        }
        
        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        
        if (isset($filters['cost_center_id'])) {
            $query->where('cost_center_id', $filters['cost_center_id']);
        }
        
        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        $entries = $query->with('account')->get();
        
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
            
            $accounts[$accountId]['debits'] += $entry->debit;
            $accounts[$accountId]['credits'] += $entry->credit;
        }
        
        $trialBalance = [];
        foreach ($accounts as $accountId => $data) {
            $account = $data['account'];
            $debits = $data['debits'];
            $credits = $data['credits'];
            
            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $debits - $credits;
            } else {
                $balance = $credits - $debits;
            }
            
            $trialBalance[] = [
                'account' => $account,
                'debits' => $debits,
                'credits' => $credits,
                'balance' => $balance,
            ];
        }
        
        return $trialBalance;
    }

    /**
     * Get general ledger with running balances
     */
    public function getGeneralLedger(array $filters = []): array
    {
        $query = GeneralLedgerEntry::query()
            ->with(['account', 'branch', 'costCenter', 'project', 'source']);
        
        if (isset($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }
        
        if (isset($filters['from_date'])) {
            $query->whereDate('entry_date', '>=', $filters['from_date']);
        }
        
        if (isset($filters['to_date'])) {
            $query->whereDate('entry_date', '<=', $filters['to_date']);
        }
        
        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        
        if (isset($filters['cost_center_id'])) {
            $query->where('cost_center_id', $filters['cost_center_id']);
        }
        
        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        return $query->orderBy('entry_date')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    /**
     * Get account statement
     */
    public function getAccountStatement(int $accountId, array $filters = []): array
    {
        $glService = app(GeneralLedgerService::class);
        
        $fromDate = isset($filters['from_date']) ? \Carbon\Carbon::parse($filters['from_date']) : null;
        $toDate = isset($filters['to_date']) ? \Carbon\Carbon::parse($filters['to_date']) : now();
        
        return $glService->getAccountStatement($accountId, $fromDate, $toDate, $filters);
    }

    /**
     * Get income statement (Revenue - Expenses)
     */
    public function getIncomeStatement(array $filters = []): array
    {
        $fromDate = isset($filters['from_date']) ? \Carbon\Carbon::parse($filters['from_date']) : null;
        $toDate = isset($filters['to_date']) ? \Carbon\Carbon::parse($filters['to_date']) : now();
        
        $query = GeneralLedgerEntry::query()
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['revenue', 'expense']);
            });
        
        if ($fromDate) {
            $query->whereDate('entry_date', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('entry_date', '<=', $toDate);
        }
        
        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        
        $entries = $query->with('account')->get();
        
        $revenue = 0;
        $expenses = 0;
        $revenueAccounts = [];
        $expenseAccounts = [];
        
        foreach ($entries as $entry) {
            $account = $entry->account;
            $accountId = $account->id;
            
            if ($account->type === 'revenue') {
                $amount = $entry->credit - $entry->debit;
                $revenue += $amount;
                
                if (!isset($revenueAccounts[$accountId])) {
                    $revenueAccounts[$accountId] = [
                        'account' => $account,
                        'amount' => 0,
                    ];
                }
                $revenueAccounts[$accountId]['amount'] += $amount;
            } else {
                $amount = $entry->debit - $entry->credit;
                $expenses += $amount;
                
                if (!isset($expenseAccounts[$accountId])) {
                    $expenseAccounts[$accountId] = [
                        'account' => $account,
                        'amount' => 0,
                    ];
                }
                $expenseAccounts[$accountId]['amount'] += $amount;
            }
        }
        
        // Convert to array format for table display
        $revenueDetails = array_map(function($item) {
            return [
                'account_code' => $item['account']->code,
                'account_name' => $item['account']->name,
                'amount' => $item['amount'],
            ];
        }, $revenueAccounts);
        
        $expenseDetails = array_map(function($item) {
            return [
                'account_code' => $item['account']->code,
                'account_name' => $item['account']->name,
                'amount' => $item['amount'],
            ];
        }, $expenseAccounts);
        
        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_income' => $revenue - $expenses,
            'revenue_details' => $revenueDetails,
            'expense_details' => $expenseDetails,
        ];
    }

    /**
     * Get balance sheet (Assets = Liabilities + Equity)
     */
    public function getBalanceSheet(\DateTime $asOfDate, array $filters = []): array
    {
        $query = GeneralLedgerEntry::query()
            ->whereDate('entry_date', '<=', $asOfDate)
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['asset', 'liability', 'equity']);
            });
        
        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        
        $entries = $query->with('account')->get();
        
        $assets = 0;
        $liabilities = 0;
        $equity = 0;
        
        foreach ($entries as $entry) {
            $account = $entry->account;
            $balance = $entry->balance;
            
            if ($account->type === 'asset') {
                $assets += $balance;
            } elseif ($account->type === 'liability') {
                $liabilities += $balance;
            } elseif ($account->type === 'equity') {
                $equity += $balance;
            }
        }
        
        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_liabilities_equity' => $liabilities + $equity,
            'is_balanced' => abs($assets - ($liabilities + $equity)) < 0.01,
        ];
    }
}

