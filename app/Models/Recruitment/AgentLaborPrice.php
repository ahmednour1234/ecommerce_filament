<?php

namespace App\Models\Recruitment;

use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLaborPrice extends Model
{
    protected $fillable = [
        'agent_id',
        'nationality_id',
        'profession_id',
        'experience_level',
        'cost_amount',
        'currency_id',
        'notes',
    ];

    protected $casts = [
        'cost_amount' => 'decimal:2',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
