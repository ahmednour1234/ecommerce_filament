<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherSignatureUsage extends Model
{
    protected $table = 'voucher_signature_usage';

    protected $fillable = [
        'voucher_id',
        'signature_id',
        'position',
        'created_by',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    /**
     * Get the voucher
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Get the signature
     */
    public function signature(): BelongsTo
    {
        return $this->belongsTo(VoucherSignature::class);
    }

    /**
     * Get the user who created this usage record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

