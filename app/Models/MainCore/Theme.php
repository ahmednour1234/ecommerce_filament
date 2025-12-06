<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'primary_color',
        'secondary_color',
        'accent_color',
        'logo_light',
        'logo_dark',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
