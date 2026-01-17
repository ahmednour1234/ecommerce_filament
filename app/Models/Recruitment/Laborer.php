<?php

namespace App\Models\Recruitment;

use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laborer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'passport_number',
        'passport_issue_place',
        'passport_issue_date',
        'passport_expiry_date',
        'birth_date',
        'gender',
        'nationality_id',
        'profession_id',
        'experience_level',
        'social_status',
        'address',
        'relative_name',
        'phone_1',
        'phone_2',
        'agent_id',
        'country_id',
        'height',
        'weight',
        'speaks_arabic',
        'speaks_english',
        'personal_image',
        'cv_file',
        'intro_video',
        'monthly_salary_amount',
        'monthly_salary_currency_id',
        'notes',
        'is_available',
        'show_on_website',
    ];

    protected $casts = [
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'birth_date' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'monthly_salary_amount' => 'decimal:2',
        'speaks_arabic' => 'boolean',
        'speaks_english' => 'boolean',
        'is_available' => 'boolean',
        'show_on_website' => 'boolean',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function salaryCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'monthly_salary_currency_id');
    }
}
