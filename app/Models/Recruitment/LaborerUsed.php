<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaborerUsed extends Model
{
    protected $table = 'laborers_used';

    protected $fillable = [
        'laborer_id',
        'agent_id',
        'used_at',
        'notes',
    ];

    protected $casts = [
        'used_at' => 'date',
    ];

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
