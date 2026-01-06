<?php

namespace App\Models\Accounting;

use App\Models\MainCore\Branch;
use App\Models\User;
use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class BankGuarantee extends Model
{
    use HasBranch;

    protected $fillable = [
        'branch_id',
        'guarantee_number',
        'issue_date',
        'start_date',
        'end_date',
        'beneficiary_name',
        'amount',
        'bank_fees',
        'original_guarantee_account_id',
        'bank_account_id',
        'bank_fees_account_id',
        'bank_fees_debit_account_id',
        'attachment_path',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'bank_fees' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guarantee) {
            // Auto-generate guarantee number if not provided
            if (empty($guarantee->guarantee_number)) {
                $guarantee->guarantee_number = static::generateGuaranteeNumber();
            }

            // Set created_by if not set
            if (empty($guarantee->created_by) && auth()->check()) {
                $guarantee->created_by = auth()->id();
            }
        });

        static::updating(function ($guarantee) {
            // Set updated_by if not set
            if (empty($guarantee->updated_by) && auth()->check()) {
                $guarantee->updated_by = auth()->id();
            }
        });
    }

    /**
     * Generate a unique guarantee number
     */
    public static function generateGuaranteeNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'BG';
            $last = static::whereNotNull('guarantee_number')
                ->where('guarantee_number', 'like', $prefix . '-%')
                ->latest('id')
                ->first();

            if ($last) {
                // Extract number from last guarantee number (e.g., BG-000001 -> 1)
                $lastNumber = (int) substr($last->guarantee_number, 3);
                $number = $lastNumber + 1;
            } else {
                $number = 1;
            }

            $guaranteeNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);

            // Ensure uniqueness (handle race conditions)
            $attempts = 0;
            while (static::where('guarantee_number', $guaranteeNumber)->exists() && $attempts < 10) {
                $number++;
                $guaranteeNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
                $attempts++;
            }

            return $guaranteeNumber;
        });
    }

    /**
     * Get the original guarantee account
     */
    public function originalGuaranteeAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'original_guarantee_account_id');
    }

    /**
     * Get the bank account
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    /**
     * Get the bank fees account
     */
    public function bankFeesAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'bank_fees_account_id');
    }

    /**
     * Get the bank fees debit account
     */
    public function bankFeesDebitAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'bank_fees_debit_account_id');
    }

    /**
     * Get all renewals
     */
    public function renewals(): HasMany
    {
        return $this->hasMany(BankGuaranteeRenewal::class);
    }

    /**
     * Get the user who created this guarantee
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this guarantee
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if guarantee is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if guarantee is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->end_date < now()->toDateString() && $this->status === 'active');
    }

    /**
     * Check if guarantee is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope to get only active guarantees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only expired guarantees
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($q2) {
                    $q2->where('status', 'active')
                        ->where('end_date', '<', now()->toDateString());
                });
        });
    }

    /**
     * Scope to get only cancelled guarantees
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Renew the guarantee
     */
    public function renew(\DateTime $newEndDate, ?string $notes = null): BankGuaranteeRenewal
    {
        $oldEndDate = $this->end_date;

        return DB::transaction(function () use ($newEndDate, $notes, $oldEndDate) {
            // Update the guarantee
            $this->update([
                'end_date' => $newEndDate->format('Y-m-d'),
                'status' => 'active', // Set back to active if it was expired
                'updated_by' => auth()->id(),
            ]);

            // Create renewal record
            return $this->renewals()->create([
                'old_end_date' => $oldEndDate,
                'new_end_date' => $newEndDate->format('Y-m-d'),
                'notes' => $notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);
        });
    }
}

