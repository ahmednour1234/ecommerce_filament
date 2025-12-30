<?php

namespace App\Services\Accounting;

use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Models\Accounting\ApprovalLog;
use App\Models\Accounting\Period;
use App\Enums\Accounting\JournalEntryStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PostingService
{
    protected GeneralLedgerService $glService;

    public function __construct(GeneralLedgerService $glService)
    {
        $this->glService = $glService;
    }

    /**
     * Post a journal entry
     */
    public function postJournalEntry(JournalEntry $entry): void
    {
        $status = JournalEntryStatus::from($entry->status ?? JournalEntryStatus::DRAFT->value);
        
        if (!$status->canBePosted()) {
            throw new \Exception(trans_dash('accounting.cannot_post', 'Entry must be approved before posting.'));
        }
        
        if ($entry->is_posted) {
            throw new \Exception(trans_dash('accounting.already_posted', 'Entry is already posted.'));
        }
        
        if (!$entry->isBalanced()) {
            throw new \Exception(trans_dash('accounting.entries_not_balanced', 'Entry is not balanced. Debits must equal credits.'));
        }
        
        // Validate period is not closed
        if ($entry->period_id) {
            $period = Period::find($entry->period_id);
            if ($period && $period->is_closed) {
                throw new \Exception(trans_dash('accounting.validation.period_closed', 'Cannot post to a closed period.'));
            }
        }
        
        DB::transaction(function () use ($entry) {
            // Generate GL entries
            $this->generateGeneralLedgerEntries($entry);
            
            // Update entry status
            $entry->update([
                'is_posted' => true,
                'posted_at' => now(),
                'status' => JournalEntryStatus::POSTED->value,
            ]);
            
            // Log approval action
            if (auth()->check()) {
                ApprovalLog::create([
                    'approvable_type' => JournalEntry::class,
                    'approvable_id' => $entry->id,
                    'action' => 'posted',
                    'user_id' => auth()->id(),
                    'notes' => 'Entry posted to general ledger',
                ]);
            }
            
            // Clear relevant caches
            $accountIds = $entry->lines()->pluck('account_id')->unique();
            foreach ($accountIds as $accountId) {
                Cache::forget("account_balance_{$accountId}");
            }
            Cache::forget('coa_tree');
        });
    }

    /**
     * Generate General Ledger entries from journal entry lines
     */
    public function generateGeneralLedgerEntries(JournalEntry $entry): void
    {
        $lines = $entry->lines()->get();
        
        foreach ($lines as $line) {
            $amount = $line->base_amount ?? ($line->debit > 0 ? $line->debit : $line->credit);
            $debit = $line->debit > 0 ? $amount : 0;
            $credit = $line->credit > 0 ? $amount : 0;
            
            // Get current balance for the account
            $currentBalance = $this->glService->getAccountBalance(
                $line->account_id,
                $entry->entry_date,
                $line->branch_id,
                $line->cost_center_id
            );
            
            // Calculate new balance
            $account = $line->account;
            $balanceChange = 0;
            if (in_array($account->type, ['asset', 'expense'])) {
                $balanceChange = $debit - $credit;
            } else {
                $balanceChange = $credit - $debit;
            }
            
            $newBalance = $currentBalance + $balanceChange;
            
            // Create GL entry
            GeneralLedgerEntry::create([
                'account_id' => $line->account_id,
                'source_type' => JournalEntryLine::class,
                'source_id' => $line->id,
                'entry_date' => $entry->entry_date,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $newBalance,
                'description' => $line->description ?? $entry->description,
                'reference' => $line->reference ?? $entry->reference,
                'branch_id' => $line->branch_id,
                'cost_center_id' => $line->cost_center_id,
                'project_id' => $line->project_id,
                'fiscal_year_id' => $entry->fiscal_year_id,
                'period_id' => $entry->period_id,
            ]);
        }
    }

    /**
     * Reverse a journal entry
     */
    public function reverseJournalEntry(JournalEntry $entry, ?string $reason = null): JournalEntry
    {
        if (!$entry->is_posted) {
            throw new \Exception(trans_dash('accounting.cannot_reverse_unposted', 'Can only reverse posted entries.'));
        }
        
        return DB::transaction(function () use ($entry, $reason) {
            // Create reversal entry
            $reversal = $entry->replicate();
            $reversal->entry_number = $entry->entry_number . '-REV';
            $reversal->entry_date = now();
            $reversal->description = ($reason ? $reason . ' - ' : '') . 'Reversal of ' . $entry->entry_number;
            $reversal->status = JournalEntryStatus::DRAFT->value;
            $reversal->is_posted = false;
            $reversal->posted_at = null;
            $reversal->approved_by = null;
            $reversal->approved_at = null;
            $reversal->rejected_by = null;
            $reversal->rejected_at = null;
            $reversal->rejection_reason = null;
            $reversal->user_id = auth()->id();
            $reversal->save();
            
            // Create reversal lines with negative amounts
            foreach ($entry->lines as $line) {
                $reversalLine = $line->replicate();
                $reversalLine->journal_entry_id = $reversal->id;
                $reversalLine->debit = $line->credit; // Swap debit and credit
                $reversalLine->credit = $line->debit;
                $reversalLine->base_amount = -($line->base_amount ?? 0);
                if ($reversalLine->amount) {
                    $reversalLine->amount = -$reversalLine->amount;
                }
                $reversalLine->save();
            }
            
            return $reversal;
        });
    }
}

