<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'key',
        'group',
        'language_id',
        'value',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

