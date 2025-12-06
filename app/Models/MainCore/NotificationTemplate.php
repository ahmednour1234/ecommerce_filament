<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'key',
        'channel_id',
        'language_id',
        'subject',
        'body_text',
        'body_html',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function channel()
    {
        return $this->belongsTo(NotificationChannel::class, 'channel_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
