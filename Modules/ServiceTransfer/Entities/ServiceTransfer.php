<?php

namespace Modules\ServiceTransfer\Entities;

use App\Models\MainCore\Branch;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Sales\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ServiceTransfer extends Model
{
    protected $table = 'service_transfers';

    protected $fillable = [
        'request_no',
        'request_date',
        'branch_id',
        'customer_id',
        'worker_id',
        'package_id',
        'nationality_id',
        'base_price',
        'external_cost',
        'government_fees',
        'tax_percent',
        'tax_value',
        'discount_percent',
        'discount_reason',
        'discount_value',
        'total_amount',
        'payment_status',
        'request_status',
        'status',
        'trial_end_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'trial_end_date' => 'date',
        'base_price' => 'decimal:2',
        'external_cost' => 'decimal:2',
        'government_fees' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            if (empty($transfer->request_no)) {
                $transfer->request_no = static::generateRequestNo();
            }
            
            if (empty($transfer->created_by) && auth()->check()) {
                $transfer->created_by = auth()->id();
            }
        });

        static::saving(function ($transfer) {
            $base = ($transfer->base_price ?? 0) + 
                   ($transfer->external_cost ?? 0) + 
                   ($transfer->government_fees ?? 0);
            
            $taxPercent = $transfer->tax_percent ?? 15;
            $taxValue = $base * ($taxPercent / 100);
            
            $discountPercent = $transfer->discount_percent ?? 0;
            $discountValue = $base * ($discountPercent / 100);
            
            $totalAmount = $base + $taxValue - $discountValue;

            $transfer->tax_value = $taxValue;
            $transfer->discount_value = $discountValue;
            $transfer->total_amount = $totalAmount;
        });

        static::saved(function ($transfer) {
            if ($transfer->wasRecentlyCreated || $transfer->wasChanged(['base_price', 'external_cost', 'government_fees', 'tax_percent', 'discount_percent', 'total_amount'])) {
                static::recalculatePaymentStatus($transfer);
            }
        });
    }

    protected static function generateRequestNo(): string
    {
        return DB::transaction(function () {
            $date = now()->format('Ymd');
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $requestNo = "REQ-{$date}-{$random}";
            
            $attempts = 0;
            while (static::where('request_no', $requestNo)->exists() && $attempts < 10) {
                $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $requestNo = "REQ-{$date}-{$random}";
                $attempts++;
            }
            
            return $requestNo;
        });
    }

    public static function recalculatePaymentStatus(ServiceTransfer $transfer): void
    {
        if ($transfer->request_status === 'refunded') {
            $transfer->payment_status = 'refunded';
            $transfer->saveQuietly();
            return;
        }

        $totalPaid = $transfer->payments()->sum('amount');
        $totalAmount = $transfer->total_amount;

        if ($totalPaid == 0) {
            $paymentStatus = 'pending';
        } elseif ($totalPaid < $totalAmount) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'paid';
        }

        if ($transfer->payment_status !== $paymentStatus) {
            $transfer->payment_status = $paymentStatus;
            $transfer->saveQuietly();
        }
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Laborer::class, 'worker_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Package::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ServiceTransferPayment::class, 'transfer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ServiceTransferDocument::class, 'service_transfer_id');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingAmount(): float
    {
        return max(0, $this->total_amount - $this->totalPaid());
    }

    public function scopeActive($query)
    {
        return $query->where('request_status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('request_status', 'archived');
    }

    public function scopeRefunded($query)
    {
        return $query->where('request_status', 'refunded');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
