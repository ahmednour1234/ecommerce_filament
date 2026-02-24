<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'logo',
        'group',
        'type',
        'is_public',
        'autoload',
    ];

    protected $casts = [
        'value'     => 'array',   // لو هتحط JSON
        'is_public' => 'boolean',
        'autoload'  => 'boolean',
    ];
}
