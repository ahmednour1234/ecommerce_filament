<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntryLine;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
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
}

