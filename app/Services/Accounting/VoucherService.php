<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Voucher;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\Journal;
use App\Enums\Accounting\JournalEntryStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherService
{
    protected PostingService $postingService;

    public function __construct(PostingService $postingService)
    {
        $this->postingService = $postingService;
    }

    /**
     * Create a receipt voucher
     */
    public function createReceiptVoucher(array $data): Voucher
    {
        return DB::transaction(function () use ($data) {
            $voucher = Voucher::create([
                'voucher_number' => $this->generateVoucherNumber('RCP'),
                'type' => 'receipt',
                'voucher_date' => $data['voucher_date'] ?? now(),
                'amount' => $data['amount'],
                'account_id' => $data['account_id'],
                'branch_id' => $data['branch_id'],
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'description' => $data['description'] ?? null,
                'reference' => $data['reference'] ?? null,
                'currency_id' => $data['currency_id'] ?? null,
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'base_amount' => $data['base_amount'] ?? $data['amount'],
                'created_by' => auth()->id(),
                'status' => JournalEntryStatus::DRAFT->value,
            ]);
            
            // Generate journal entry
            $this->generateJournalEntryForVoucher($voucher);
            
            return $voucher;
        });
    }

    /**
     * Create a payment voucher
     */
    public function createPaymentVoucher(array $data): Voucher
    {
        return DB::transaction(function () use ($data) {
            $voucher = Voucher::create([
                'voucher_number' => $this->generateVoucherNumber('PAY'),
                'type' => 'payment',
                'voucher_date' => $data['voucher_date'] ?? now(),
                'amount' => $data['amount'],
                'account_id' => $data['account_id'],
                'branch_id' => $data['branch_id'],
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'description' => $data['description'] ?? null,
                'reference' => $data['reference'] ?? null,
                'currency_id' => $data['currency_id'] ?? null,
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'base_amount' => $data['base_amount'] ?? $data['amount'],
                'created_by' => auth()->id(),
                'status' => JournalEntryStatus::DRAFT->value,
            ]);
            
            // Generate journal entry
            $this->generateJournalEntryForVoucher($voucher);
            
            return $voucher;
        });
    }

    /**
     * Generate journal entry for voucher
     */
    protected function generateJournalEntryForVoucher(Voucher $voucher): void
    {
        $journal = Journal::where('type', $voucher->type)->first();
        if (!$journal) {
            throw new \Exception(trans_dash('accounting.journal_not_found', 'Journal not found for voucher type.'));
        }
        
        $entry = JournalEntry::create([
            'journal_id' => $journal->id,
            'entry_number' => $voucher->voucher_number,
            'entry_date' => $voucher->voucher_date,
            'reference' => $voucher->reference,
            'description' => $voucher->description ?? ($voucher->type === 'receipt' ? 'Receipt Voucher' : 'Payment Voucher'),
            'branch_id' => $voucher->branch_id,
            'cost_center_id' => $voucher->cost_center_id,
            'user_id' => $voucher->created_by,
            'status' => $voucher->status,
            'fiscal_year_id' => $voucher->fiscal_year_id,
            'period_id' => $voucher->period_id,
        ]);
        
        $baseAmount = $voucher->base_amount ?? $voucher->amount;
        
        if ($voucher->type === 'receipt') {
            // Receipt: Debit cash/bank account, Credit source account
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $voucher->account_id,
                'debit' => $baseAmount,
                'credit' => 0,
                'description' => $voucher->description,
                'branch_id' => $voucher->branch_id,
                'cost_center_id' => $voucher->cost_center_id,
                'project_id' => $voucher->project_id,
                'currency_id' => $voucher->currency_id,
                'exchange_rate' => $voucher->exchange_rate,
                'amount' => $voucher->amount,
                'base_amount' => $baseAmount,
                'reference' => $voucher->reference,
            ]);
            
            // Credit side - would need to be specified in data
            if (isset($voucher->credit_account_id)) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $voucher->credit_account_id,
                    'debit' => 0,
                    'credit' => $baseAmount,
                    'description' => $voucher->description,
                    'branch_id' => $voucher->branch_id,
                    'cost_center_id' => $voucher->cost_center_id,
                    'project_id' => $voucher->project_id,
                    'currency_id' => $voucher->currency_id,
                    'exchange_rate' => $voucher->exchange_rate,
                    'amount' => $voucher->amount,
                    'base_amount' => $baseAmount,
                    'reference' => $voucher->reference,
                ]);
            }
        } else {
            // Payment: Debit destination account, Credit cash/bank account
            if (isset($voucher->debit_account_id)) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $voucher->debit_account_id,
                    'debit' => $baseAmount,
                    'credit' => 0,
                    'description' => $voucher->description,
                    'branch_id' => $voucher->branch_id,
                    'cost_center_id' => $voucher->cost_center_id,
                    'project_id' => $voucher->project_id,
                    'currency_id' => $voucher->currency_id,
                    'exchange_rate' => $voucher->exchange_rate,
                    'amount' => $voucher->amount,
                    'base_amount' => $baseAmount,
                    'reference' => $voucher->reference,
                ]);
            }
            
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $voucher->account_id,
                'debit' => 0,
                'credit' => $baseAmount,
                'description' => $voucher->description,
                'branch_id' => $voucher->branch_id,
                'cost_center_id' => $voucher->cost_center_id,
                'project_id' => $voucher->project_id,
                'currency_id' => $voucher->currency_id,
                'exchange_rate' => $voucher->exchange_rate,
                'amount' => $voucher->amount,
                'base_amount' => $baseAmount,
                'reference' => $voucher->reference,
            ]);
        }
        
        $voucher->update(['journal_entry_id' => $entry->id]);
    }

    /**
     * Generate voucher number
     */
    protected function generateVoucherNumber(string $prefix): string
    {
        $lastVoucher = Voucher::where('type', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastVoucher ? ((int) substr($lastVoucher->voucher_number, -6)) + 1 : 1;
        
        return $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

