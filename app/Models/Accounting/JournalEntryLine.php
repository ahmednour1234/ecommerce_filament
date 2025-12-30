<?php

namespace App\Models\Accounting;

use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use App\Traits\HasBranch;
use App\Traits\HasCostCenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryLine extends Model
{
    use HasBranch, HasCostCenter;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'description',
        'branch_id',
        'cost_center_id',
        'project_id',
        'currency_id',
        'exchange_rate',
        'amount',
        'base_amount',
        'reference',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'amount' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:8',
    ];

    /**
     * Get the journal entry
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
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
     * Get the cost center
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the currency
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Check if line has valid debit/credit (only one should be > 0)
     */
    public function isValid(): bool
    {
        return ($this->debit > 0 && $this->credit == 0) || 
               ($this->credit > 0 && $this->debit == 0);
    }

    /**
     * Get amount in base currency
     */
    public function getAmountInBaseCurrencyAttribute(): float
    {
        if ($this->base_amount !== null) {
            return (float) $this->base_amount;
        }
        
        if ($this->amount && $this->exchange_rate) {
            return (float) ($this->amount * $this->exchange_rate);
        }
        
        return $this->debit > 0 ? (float) $this->debit : (float) $this->credit;
    }
}

