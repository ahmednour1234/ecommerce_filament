<?php

namespace App\Models;

use App\Models\MainCore\Branch;
use App\Models\Recruitment\Nationality;
use App\Models\User;
use App\Services\ComplaintService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'complaint_description',
        'attachment',
        'status',
        'priority',
        'branch_id',
        'assigned_to',
        'resolution_notes',
        'branch_action_taken',
        'resolved_at',
        'in_progress_at',
        'created_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'in_progress_at' => 'datetime',
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
            if ($complaint->isDirty('status')) {
                $oldStatus = $complaint->getOriginal('status');
                $newStatus = $complaint->status;

                if ($newStatus === 'in_progress' && !$complaint->in_progress_at) {
                    $complaint->in_progress_at = now();
                }
                if ($newStatus === 'resolved' && !$complaint->resolved_at) {
                    $complaint->resolved_at = now();
                }

                if ($oldStatus !== $newStatus) {
                    \App\Models\ComplaintStatusLog::create([
                        'complaint_id' => $complaint->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'status_date' => now(),
                        'created_by' => auth()->id(),
                    ]);
                }
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

    public function statusLogs(): HasMany
    {
        return $this->hasMany(\App\Models\ComplaintStatusLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\ComplaintNotification::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(\App\Models\ComplaintMessage::class);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
