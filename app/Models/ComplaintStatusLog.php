<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintStatusLog extends Model
{
    protected $table = 'complaint_status_logs';

    protected $fillable = [
        'complaint_id',
        'old_status',
        'new_status',
        'status_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'status_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
