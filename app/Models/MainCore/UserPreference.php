<?php

namespace App\Models\MainCore;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'language_id',
        'theme_id',
        'timezone',
        'date_format',
        'time_format',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
