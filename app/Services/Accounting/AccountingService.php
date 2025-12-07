<?php

namespace App\Services\Accounting;

use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AccountingService
{
    /**
     * Validate journal entry balance
     */
    public function validateBalance(JournalEntry $entry): array
    {
        $totalDebits = $entry->lines()->sum('debit');
        $totalCredits = $entry->lines()->sum('credit');
        $difference = abs($totalDebits - $totalCredits);

        return [
            'is_balanced' => $difference < 0.01,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => $difference,
        ];
    }

    /**
     * Post a journal entry
     */
    public function postEntry(JournalEntry $entry): bool
    {
        if ($entry->is_posted) {
            throw new \Exception('Entry is already posted.');
        }

        $validation = $this->validateBalance($entry);
        if (!$validation['is_balanced']) {
            throw new \Exception('Entry is not balanced. Debits: ' . $validation['total_debits'] . ', Credits: ' . $validation['total_credits']);
        }

        DB::transaction(function () use ($entry) {
            $entry->update([
                'is_posted' => true,
                'posted_at' => now(),
            ]);

            // Clear any cached account balances
            $accountIds = $entry->lines()->pluck('account_id')->unique();
            foreach ($accountIds as $accountId) {
                Cache::forget("account_balance_{$accountId}");
            }
        });

        return true;
    }

    /**
     * Get account balance up to a specific date
     */
    public function getAccountBalance(int $accountId, ?\DateTime $asOfDate = null): float
    {
        $cacheKey = "account_balance_{$accountId}_" . ($asOfDate ? $asOfDate->format('Y-m-d') : 'all');
        
        return Cache::remember($cacheKey, 3600, function () use ($accountId, $asOfDate) {
            $query = JournalEntryLine::where('account_id', $accountId)
                ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                    $q->where('is_posted', true);
                    if ($asOfDate) {
                        $q->whereDate('entry_date', '<=', $asOfDate);
                    }
                });

            $debits = (float) $query->sum('debit');
            $credits = (float) $query->sum('credit');

            // Get account type to determine if debits increase or decrease balance
            $account = Account::find($accountId);
            if (!$account) {
                return 0;
            }

            // Assets and Expenses: Debits increase, Credits decrease
            // Liabilities, Equity, Revenue: Credits increase, Debits decrease
            $balance = 0;
            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $debits - $credits;
            } else {
                $balance = $credits - $debits;
            }

            return $balance;
        });
    }

    /**
     * Get account balance by branch
     */
    public function getAccountBalanceByBranch(int $accountId, int $branchId, ?\DateTime $asOfDate = null): float
    {
        $query = JournalEntryLine::where('account_id', $accountId)
            ->where('branch_id', $branchId)
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->where('is_posted', true);
                if ($asOfDate) {
                    $q->whereDate('entry_date', '<=', $asOfDate);
                }
            });

        $debits = (float) $query->sum('debit');
        $credits = (float) $query->sum('credit');

        $account = Account::find($accountId);
        if (!$account) {
            return 0;
        }

        if (in_array($account->type, ['asset', 'expense'])) {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

    /**
     * Get account balance by cost center
     */
    public function getAccountBalanceByCostCenter(int $accountId, int $costCenterId, ?\DateTime $asOfDate = null): float
    {
        $query = JournalEntryLine::where('account_id', $accountId)
            ->where('cost_center_id', $costCenterId)
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->where('is_posted', true);
                if ($asOfDate) {
                    $q->whereDate('entry_date', '<=', $asOfDate);
                }
            });

        $debits = (float) $query->sum('debit');
        $credits = (float) $query->sum('credit');

        $account = Account::find($accountId);
        if (!$account) {
            return 0;
        }

        if (in_array($account->type, ['asset', 'expense'])) {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

    /**
     * Generate trial balance
     */
    public function getTrialBalance(?\DateTime $asOfDate = null, ?int $branchId = null, ?int $costCenterId = null): array
    {
        $query = JournalEntryLine::query()
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->where('is_posted', true);
                if ($asOfDate) {
                    $q->whereDate('entry_date', '<=', $asOfDate);
                }
            })
            ->with('account');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }

        $lines = $query->get();
        $accounts = [];

        foreach ($lines as $line) {
            $accountId = $line->account_id;
            if (!isset($accounts[$accountId])) {
                $accounts[$accountId] = [
                    'account' => $line->account,
                    'debits' => 0,
                    'credits' => 0,
                ];
            }

            $accounts[$accountId]['debits'] += $line->debit;
            $accounts[$accountId]['credits'] += $line->credit;
        }

        // Calculate balances
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
}

