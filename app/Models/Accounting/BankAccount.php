<?php

namespace App\Models\Accounting;

use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    protected $fillable = [
        'account_id',
        'bank_name',
        'account_number',
        'iban',
        'swift_code',
        'branch_id',
        'currency_id',
        'opening_balance',
        'current_balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new bank account, set current balance to opening balance
        static::creating(function ($bankAccount) {
            if ($bankAccount->current_balance === null || $bankAccount->current_balance == 0) {
                $bankAccount->current_balance = $bankAccount->opening_balance ?? 0;
            }
        });

        // When updating opening balance, recalculate current balance if no transactions exist
        static::updating(function ($bankAccount) {
            if ($bankAccount->isDirty('opening_balance')) {
                // If current balance equals old opening balance, update it to new opening balance
                // This handles the case where no transactions have been posted yet
                $oldOpeningBalance = $bankAccount->getOriginal('opening_balance') ?? 0;
                if ($bankAccount->current_balance == $oldOpeningBalance) {
                    $bankAccount->current_balance = $bankAccount->opening_balance;
                } else {
                    // Recalculate from account transactions
                    $bankAccount->recalculateCurrentBalance();
                }
            }
        });
    }

    /**
     * Get the account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the currency
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Scope to get only active bank accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Recalculate current balance from account transactions
     * Current balance = Opening balance + (all posted transactions affecting this account)
     */
    public function recalculateCurrentBalance(): void
    {
        if (!$this->account_id) {
            return;
        }

        // Get account balance from General Ledger or Journal Entries
        $glService = app(\App\Services\Accounting\GeneralLedgerService::class);
        
        // Get balance from General Ledger (includes opening balance if there's an opening entry)
        $accountBalance = $glService->getAccountBalance(
            $this->account_id,
            null, // All dates
            $this->branch_id,
            null // cost_center_id
        );

        // If no GL entries exist, calculate from opening balance + journal entries
        if ($accountBalance == 0) {
            $accountingService = app(\App\Services\Accounting\AccountingService::class);
            $transactionBalance = $accountingService->getAccountBalance($this->account_id);
            
            // Current balance = Opening balance + transaction balance
            $this->current_balance = ($this->opening_balance ?? 0) + $transactionBalance;
        } else {
            // GL balance already includes opening balance if it was posted
            // But we need to ensure it starts from opening balance
            // If this is a new account with no transactions, use opening balance
            $hasTransactions = \App\Models\Accounting\GeneralLedgerEntry::where('account_id', $this->account_id)
                ->exists();
            
            if (!$hasTransactions) {
                $this->current_balance = $this->opening_balance ?? 0;
            } else {
                // For existing accounts, the GL balance should already be correct
                // But we need to account for opening balance if it wasn't posted as a transaction
                $this->current_balance = $accountBalance;
            }
        }

        // Save without triggering events to avoid recursion
        $this->saveQuietly();
    }

    /**
     * Get calculated current balance (real-time calculation)
     */
    public function getCalculatedCurrentBalanceAttribute(): float
    {
        if (!$this->account_id) {
            return $this->opening_balance ?? 0;
        }

        $glService = app(\App\Services\Accounting\GeneralLedgerService::class);
        $accountBalance = $glService->getAccountBalance(
            $this->account_id,
            null,
            $this->branch_id,
            null
        );

        // If no transactions, return opening balance
        if ($accountBalance == 0) {
            $hasTransactions = \App\Models\Accounting\GeneralLedgerEntry::where('account_id', $this->account_id)
                ->exists();
            
            if (!$hasTransactions) {
                return $this->opening_balance ?? 0;
            }
        }

        return $accountBalance;
    }
}
