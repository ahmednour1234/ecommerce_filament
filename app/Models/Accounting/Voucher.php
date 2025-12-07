<?php

namespace App\Models\Accounting;

use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\User;
use App\Traits\HasBranch;
use App\Traits\HasCostCenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasBranch, HasCostCenter;

    protected $fillable = [
        'voucher_number',
        'type',
        'voucher_date',
        'amount',
        'account_id',
        'journal_entry_id',
        'branch_id',
        'cost_center_id',
        'description',
        'reference',
        'created_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the journal entry (if auto-generated)
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
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
     * Get the user who created this voucher
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if voucher is a payment voucher
     */
    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    /**
     * Check if voucher is a receipt voucher
     */
    public function isReceipt(): bool
    {
        return $this->type === 'receipt';
    }
}

