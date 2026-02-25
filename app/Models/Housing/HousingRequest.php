<?php

namespace App\Models\Housing;

use App\Models\Client;
use App\Models\MainCore\Branch;
use App\Models\Recruitment\Laborer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingRequest extends Model
{
    use SoftDeletes;

    protected $table = 'housing_requests';

    protected $fillable = [
        'order_no',
        'contract_no',
        'client_id',
        'laborer_id',
        'passport_no',
        'sponsor_name',
        'transferred_sponsor_name',
        'request_type',
        'housing_type',
        'request_date',
        'requested_from',
        'requested_to',
        'building_id',
        'unit_id',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'branch_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'requested_from' => 'date',
        'requested_to' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->order_no)) {
                $request->order_no = static::generateOrderNo();
            }
            
            if (empty($request->created_by) && auth()->check()) {
                $request->created_by = auth()->id();
            }
        });
    }

    public static function generateOrderNo(): string
    {
        $prefix = 'HRQ';
        $last = static::whereNotNull('order_no')
            ->where('order_no', 'like', $prefix . '-%')
            ->latest('id')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->order_no, 4);
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }

        return $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(HousingAssignment::class, 'laborer_id', 'laborer_id')
            ->whereNull('end_date');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePending($query)
    {
        // Note: This scope may need to be updated based on business logic
        // For now, keeping it but it won't work with the new status field
        // You may want to remove this or update it to check for a specific status value
        return $query;
    }

    public function scopeDelivery($query)
    {
        return $query->where('request_type', 'delivery');
    }

    public function scopeReturn($query)
    {
        return $query->where('request_type', 'return');
    }

    public function scopeRecruitment($query)
    {
        return $query->where('housing_type', 'recruitment');
    }

    public function scopeRental($query)
    {
        return $query->where('housing_type', 'rental');
    }
}
