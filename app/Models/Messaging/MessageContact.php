<?php

namespace App\Models\Messaging;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageContact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'phone',
        'source',
    ];

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }
}
