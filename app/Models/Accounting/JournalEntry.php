<?php

namespace App\Models\Accounting;

use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\User;
use App\Traits\HasBranch;
use App\Traits\HasCostCenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasBranch, HasCostCenter;

    protected $fillable = [
        'journal_id',
        'entry_number',
        'entry_date',
        'reference',
        'description',
        'branch_id',
        'cost_center_id',
        'user_id',
        'is_posted',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    /**
     * Get the journal
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
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
     * Get the user who created this entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all journal entry lines
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class)->orderBy('id');
    }

    /**
     * Get total debits
     */
    public function getTotalDebitsAttribute(): float
    {
        return $this->lines()->sum('debit');
    }

    /**
     * Get total credits
     */
    public function getTotalCreditsAttribute(): float
    {
        return $this->lines()->sum('credit');
    }

    /**
     * Check if entry is balanced (debits = credits)
     */
    public function isBalanced(): bool
    {
        return abs($this->total_debits - $this->total_credits) < 0.01; // Allow small floating point differences
    }

    /**
     * Get balance difference
     */
    public function getBalanceDifferenceAttribute(): float
    {
        return abs($this->total_debits - $this->total_credits);
    }

    /**
     * Scope to get only posted entries
     */
    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    /**
     * Scope to get only unposted entries
     */
    public function scopeUnposted($query)
    {
        return $query->where('is_posted', false);
    }
}

