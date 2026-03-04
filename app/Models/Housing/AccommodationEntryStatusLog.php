<?php

namespace App\Models\Housing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodationEntryStatusLog extends Model
{
    protected $table = 'accommodation_entry_status_logs';

    protected $fillable = [
        'accommodation_entry_id',
        'old_status_id',
        'new_status_id',
        'status_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status_date' => 'date',
    ];

    public function accommodationEntry(): BelongsTo
    {
        return $this->belongsTo(AccommodationEntry::class);
    }

    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(HousingStatus::class, 'old_status_id');
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(HousingStatus::class, 'new_status_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
