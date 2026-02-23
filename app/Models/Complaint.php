<?php

namespace App\Models;

use App\Models\MainCore\Branch;
use App\Models\Recruitment\Nationality;
use App\Models\User;
use App\Services\ComplaintService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'complaint_no',
        'contract_type',
        'contract_id',
        'problem_type',
        'phone_number',
        'nationality_id',
        'subject',
        'description',
        'status',
        'priority',
        'branch_id',
        'assigned_to',
        'resolution_notes',
        'resolved_at',
        'created_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {
            if (empty($complaint->complaint_no)) {
                $service = app(ComplaintService::class);
                $complaint->complaint_no = $service->generateComplaintNo();
            }
            
            if (empty($complaint->created_by) && auth()->check()) {
                $complaint->created_by = auth()->id();
            }
        });

        static::updating(function ($complaint) {
            if ($complaint->isDirty('status') && $complaint->status === 'resolved' && !$complaint->resolved_at) {
                $complaint->resolved_at = now();
            }
        });
    }

    public function complaintable(): MorphTo
    {
        return $this->morphTo('contract');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeLow($query)
    {
        return $query->where('priority', 'low');
    }

    public function scopeMedium($query)
    {
        return $query->where('priority', 'medium');
    }

    public function scopeHigh($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }
}
