<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentContractStatusLog extends Model
{
    protected $fillable = [
        'recruitment_contract_id',
        'old_status',
        'new_status',
        'status_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status_date' => 'date',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(RecruitmentContract::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
