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
        'type',
        'request_date',
        'status_id',
        'notes',
        'branch_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'request_date' => 'date',
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

    public function status(): BelongsTo
    {
        return $this->belongsTo(HousingStatus::class, 'status_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
        return $query->whereHas('status', function ($q) {
            $q->where('key', 'pending');
        });
    }

    public function scopeDelivery($query)
    {
        return $query->where('type', 'delivery');
    }

    public function scopeReturn($query)
    {
        return $query->where('type', 'return');
    }
}
