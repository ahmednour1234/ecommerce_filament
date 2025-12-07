<?php

namespace App\Models\MainCore;

use App\Traits\HasFileUploads;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFileUploads;

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

    /**
     * Get logo light URL
     */
    public function getLogoLightUrlAttribute(): ?string
    {
        return $this->logo_light ? $this->getFileUrl($this->logo_light) : null;
    }

    /**
     * Get logo dark URL
     */
    public function getLogoDarkUrlAttribute(): ?string
    {
        return $this->logo_dark ? $this->getFileUrl($this->logo_dark) : null;
    }
}
