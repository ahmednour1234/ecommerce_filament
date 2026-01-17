<?php

namespace App\Models\Recruitment;

use App\Models\MainCore\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'email',
        'country_id',
        'city_ar',
        'city_en',
        'address_ar',
        'address_en',
        'license_number',
        'phone_1',
        'phone_2',
        'mobile',
        'fax',
        'responsible_name',
        'passport_number',
        'passport_issue_date',
        'passport_issue_place',
        'bank_sender',
        'account_number',
        'username',
        'notes',
    ];

    protected $casts = [
        'passport_issue_date' => 'date',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function laborPrices(): HasMany
    {
        return $this->hasMany(AgentLaborPrice::class);
    }
}
