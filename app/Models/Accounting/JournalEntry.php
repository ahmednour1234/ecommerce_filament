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
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'status',
        'is_posted',
        'posted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'fiscal_year_id',
        'period_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
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
     * Get the user who approved this entry
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected this entry
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the fiscal year
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    /**
     * Get the period
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Get all journal entry lines
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class)->orderBy('id');
    }

    /**
     * Get attachments
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get approval logs
     */
    public function approvalLogs(): MorphMany
    {
        return $this->morphMany(ApprovalLog::class, 'approvable');
    }

    /**
     * Get general ledger entries
     */
    public function generalLedgerEntries(): MorphMany
    {
        return $this->morphMany(GeneralLedgerEntry::class, 'source');
    }

    /**
     * Get total debits using database aggregation
     */
    public function getTotalDebitsAttribute(): float
    {
        if (!$this->relationLoaded('lines')) {
            // Use database aggregation for better performance
            $result = $this->lines()
                ->where('debit', '>', 0)
                ->selectRaw('SUM(COALESCE(base_amount, debit)) as total')
                ->value('total');
            return (float) ($result ?? 0);
        }

        // If lines are already loaded, calculate from collection
        $total = 0;
        foreach ($this->lines as $line) {
            if ($line->debit > 0) {
                $baseAmount = $line->base_amount ?? $line->debit;
                $total += $baseAmount;
            }
        }
        return $total;
    }

    /**
     * Get total credits using database aggregation
     */
    public function getTotalCreditsAttribute(): float
    {
        if (!$this->relationLoaded('lines')) {
            // Use database aggregation for better performance
            $result = $this->lines()
                ->where('credit', '>', 0)
                ->selectRaw('SUM(COALESCE(base_amount, credit)) as total')
                ->value('total');
            return (float) ($result ?? 0);
        }

        // If lines are already loaded, calculate from collection
        $total = 0;
        foreach ($this->lines as $line) {
            if ($line->credit > 0) {
                $baseAmount = $line->base_amount ?? $line->credit;
                $total += $baseAmount;
            }
        }
        return $total;
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
     * Check if entry is draft
     */
    public function isDraft(): bool
    {
        return ($this->status ?? 'draft') === 'draft';
    }

    /**
     * Check if entry is pending approval
     */
    public function isPendingApproval(): bool
    {
        return ($this->status ?? 'draft') === 'pending_approval';
    }

    /**
     * Check if entry is approved
     */
    public function isApproved(): bool
    {
        return ($this->status ?? 'draft') === 'approved';
    }

    /**
     * Check if entry is rejected
     */
    public function isRejected(): bool
    {
        return ($this->status ?? 'draft') === 'rejected';
    }

    /**
     * Check if entry can be edited
     */
    public function canBeEdited(): bool
    {
        if ($this->is_posted) {
            return false;
        }
        $status = $this->status ?? 'draft';
        return in_array($status, ['draft', 'rejected']);
    }

    /**
     * Check if entry can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_posted && ($this->status ?? 'draft') === 'draft';
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

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by journal
     */
    public function scopeByJournal($query, int $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    /**
     * Scope to filter by branch
     */
    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by period
     */
    public function scopeByPeriod($query, int $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    /**
     * Scope to filter by fiscal year
     */
    public function scopeByFiscalYear($query, int $fiscalYearId)
    {
        return $query->where('fiscal_year_id', $fiscalYearId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Scope to eager load common relationships
     */
    public function scopeWithDetails($query)
    {
        return $query->with([
            'journal',
            'branch',
            'costCenter',
            'user',
            'fiscalYear',
            'period',
            'lines.account',
            'lines.currency',
        ]);
    }
}

