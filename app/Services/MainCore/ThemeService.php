<?php

namespace App\Services\MainCore;

use App\Models\MainCore\Theme;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public function defaultTheme(): ?Theme
    {
        return Cache::remember('maincore.theme.default', now()->addDay(), function () {
            return Theme::where('is_default', true)->first();
        });
    }

    public function color(string $key, ?string $default = null): ?string
    {
        $theme = $this->defaultTheme();

        if (!$theme) {
            return $default;
        }

        return $theme->{$key} ?? $default;
    }

    public function logo(?string $variant = 'light'): ?string
    {
        $theme = $this->defaultTheme();

        if (!$theme) {
            return null;
        }

        return $variant === 'dark' ? $theme->logo_dark : $theme->logo_light;
    }
}
