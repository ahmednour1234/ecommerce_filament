<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Models\Accounting\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class GeneralLedgerService
{
    /**
     * Get account balance up to a specific date
     */
    public function getAccountBalance(
        int $accountId,
        ?\DateTime $asOfDate = null,
        ?int $branchId = null,
        ?int $costCenterId = null
    ): float {
        $query = GeneralLedgerEntry::where('account_id', $accountId);
        
        if ($asOfDate) {
            $query->whereDate('entry_date', '<=', $asOfDate);
        }
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        // Get the latest balance for this account
        $latestEntry = $query->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        
        return $latestEntry ? (float) $latestEntry->balance : 0.0;
    }

    /**
     * Get account statement
     */
    public function getAccountStatement(
        int $accountId,
        ?\DateTime $fromDate = null,
        ?\DateTime $toDate = null,
        array $filters = []
    ): array {
        $query = GeneralLedgerEntry::where('account_id', $accountId)
            ->with(['source', 'branch', 'costCenter', 'project']);
        
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
        
        return $query->orderBy('entry_date')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    /**
     * Recalculate running balances for an account
     */
    public function recalculateBalances(int $accountId, ?\DateTime $fromDate = null): void
    {
        $account = Account::find($accountId);
        if (!$account) {
            return;
        }
        
        $query = GeneralLedgerEntry::where('account_id', $accountId)
            ->orderBy('entry_date')
            ->orderBy('id');
        
        if ($fromDate) {
            $query->whereDate('entry_date', '>=', $fromDate);
        }
        
        $entries = $query->get();
        $runningBalance = $fromDate ? $this->getAccountBalance($accountId, (clone $fromDate)->modify('-1 day')) : 0;
        
        foreach ($entries as $entry) {
            $balanceChange = 0;
            if (in_array($account->type, ['asset', 'expense'])) {
                $balanceChange = $entry->debit - $entry->credit;
            } else {
                $balanceChange = $entry->credit - $entry->debit;
            }
            
            $runningBalance += $balanceChange;
            $entry->update(['balance' => $runningBalance]);
        }
    }
}

