<?php

namespace App\Services\Accounting;

use App\Models\Accounting\BankGuarantee;
use App\Models\Accounting\Journal;
use App\Models\Accounting\JournalEntry;
use Illuminate\Support\Facades\DB;

class BankGuaranteeService
{
    /**
     * Create journal entry for a bank guarantee
     * 
     * This method creates accounting entries:
     * 1. Debit original_guarantee_account_id by amount
     * 2. Credit bank_account_id by amount
     * 3. If bank_fees > 0:
     *    - Debit bank_fees_account_id by bank_fees
     *    - Credit bank_account_id (or bank_fees_debit_account_id if set) by bank_fees
     * 
     * @param BankGuarantee $guarantee
     * @return JournalEntry|null
     */
    public function createJournalEntry(BankGuarantee $guarantee): ?JournalEntry
    {
        // Check if journal entry system is available
        if (!class_exists(JournalEntry::class) || !class_exists(JournalEntryService::class)) {
            // Journal entry system not available, skip
            return null;
        }

        return DB::transaction(function () use ($guarantee) {
            // Find a suitable journal (general or cash)
            $journal = Journal::where('type', 'general')->first() 
                ?? Journal::where('type', 'cash')->first();

            if (!$journal) {
                throw new \Exception('No suitable journal found. Please create a general or cash journal first.');
            }

            // Generate entry number
            $prefix = strtoupper(substr($journal->code, 0, 3));
            $lastEntry = JournalEntry::where('journal_id', $journal->id)->latest('id')->first();
            $number = $lastEntry ? ((int) substr($lastEntry->entry_number, -6)) + 1 : 1;
            $entryNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);

            // Create journal entry
            $entry = JournalEntry::create([
                'journal_id' => $journal->id,
                'entry_number' => $entryNumber,
                'entry_date' => $guarantee->issue_date,
                'reference' => $guarantee->guarantee_number,
                'description' => 'Bank Guarantee: ' . $guarantee->beneficiary_name,
                'branch_id' => $guarantee->branch_id,
                'user_id' => $guarantee->created_by ?? auth()->id(),
                'status' => 'draft',
                'is_posted' => false,
            ]);

            $journalEntryService = app(JournalEntryService::class);
            $lines = [];

            // Use base_amount if available, otherwise use amount
            $amount = $guarantee->base_amount ?? $guarantee->amount;
            $bankFees = $guarantee->base_bank_fees ?? $guarantee->bank_fees;

            // Line 1: Debit original guarantee account
            $lines[] = [
                'account_id' => $guarantee->original_guarantee_account_id,
                'debit' => $amount,
                'credit' => 0,
                'description' => 'Bank Guarantee: ' . $guarantee->beneficiary_name,
                'branch_id' => $guarantee->branch_id,
            ];

            // Line 2: Credit bank account
            $lines[] = [
                'account_id' => $guarantee->bank_account_id,
                'debit' => 0,
                'credit' => $amount,
                'description' => 'Bank Guarantee: ' . $guarantee->beneficiary_name,
                'branch_id' => $guarantee->branch_id,
            ];

            // If bank fees > 0, add fees entries
            if ($bankFees > 0 && $guarantee->bank_fees_account_id) {
                // Line 3: Debit bank fees account
                $lines[] = [
                    'account_id' => $guarantee->bank_fees_account_id,
                    'debit' => $bankFees,
                    'credit' => 0,
                    'description' => 'Bank Fees for Guarantee: ' . $guarantee->guarantee_number,
                    'branch_id' => $guarantee->branch_id,
                ];

                // Line 4: Credit bank account (or fees debit account if set)
                $feesCreditAccountId = $guarantee->bank_fees_debit_account_id ?? $guarantee->bank_account_id;
                $lines[] = [
                    'account_id' => $feesCreditAccountId,
                    'debit' => 0,
                    'credit' => $bankFees,
                    'description' => 'Bank Fees for Guarantee: ' . $guarantee->guarantee_number,
                    'branch_id' => $guarantee->branch_id,
                ];
            }

            // Create lines using the service
            $journalEntryService->createLines($entry, $lines);

            return $entry->fresh(['lines']);
        });
    }
}

