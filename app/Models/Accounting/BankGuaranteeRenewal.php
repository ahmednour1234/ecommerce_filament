<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankGuaranteeRenewal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'bank_guarantee_id',
        'old_end_date',
        'new_end_date',
        'notes',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'old_end_date' => 'date',
        'new_end_date' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Get the bank guarantee
     */
    public function bankGuarantee(): BelongsTo
    {
        return $this->belongsTo(BankGuarantee::class);
    }

    /**
     * Get the user who created this renewal
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

