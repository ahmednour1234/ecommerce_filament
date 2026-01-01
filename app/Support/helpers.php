<?php

if (!function_exists('trans_dash')) {
    /**
     * Get dashboard translation with optional replacements
     */
    function trans_dash(string $key, ?string $default = null, array|string|null $replace = null, ?string $languageCode = null): string
    {
        // Handle backward compatibility: if 3rd param is string, it's language code
        if (is_string($replace) && $languageCode === null) {
            $languageCode = $replace;
            $replace = null;
        }
        
        $translation = app(\App\Services\MainCore\TranslationService::class)
            ->get($key, $languageCode, 'dashboard', $default ?? $key);
        
        // If $replace is an array, replace placeholders
        if (is_array($replace)) {
            foreach ($replace as $placeholder => $value) {
                $translation = str_replace(':' . $placeholder, (string) $value, $translation);
            }
        }
        
        return $translation;
    }
}

if (!function_exists('set_locale')) {
    /**
     * Set current locale
     */
    function set_locale(string $languageCode): void
    {
        app(\App\Services\MainCore\TranslationService::class)
            ->setLanguage($languageCode);
    }
}

if (!function_exists('get_locale')) {
    /**
     * Get current locale code
     */
    function get_locale(): string
    {
        return app(\App\Services\MainCore\TranslationService::class)
            ->getCurrentLanguageCode();
    }
}

if (!function_exists('tr')) {
    /**
     * Get translation with fallback: current locale → 'en' → default → key
     * 
     * Usage:
     * - tr('key') - uses 'dashboard' group
     * - tr('key', 'default') - old usage, uses 'menu' group for backward compatibility
     * - tr('key', [], 'en', 'sidebar') - new usage with group support
     * 
     * @param string $key Translation key (e.g., 'sidebar.accounting', 'pages.reports.title')
     * @param array|string|null $replace Replacements array or default string (for backward compatibility)
     * @param string|null $locale Locale code (optional)
     * @param string $group Translation group (default: 'dashboard')
     * @return string Translated text
     */
    function tr(string $key, array|string|null $replace = [], ?string $locale = null, string $group = 'dashboard'): string
    {
        $default = null;
        $replacements = [];
        
        // Backward compatibility: if second param is string and only 2 args, it's old usage
        if (func_num_args() === 2 && is_string($replace)) {
            // Old usage: tr($key, $default) - use 'menu' group for backward compatibility
            $translation = app(\App\Services\MainCore\TranslationService::class)
                ->get($key, null, 'menu', $replace ?? $key);
            return $translation;
        }
        
        // New usage: handle replace array or default
        if (is_string($replace)) {
            $default = $replace;
        } elseif (is_array($replace)) {
            $replacements = $replace;
        }
        
        // Get translation
        $translation = app(\App\Services\MainCore\TranslationService::class)
            ->get($key, $locale, $group, $default ?? $key);
        
        // Apply replacements if provided
        if (!empty($replacements)) {
            foreach ($replacements as $placeholder => $value) {
                $translation = str_replace(':' . $placeholder, (string) $value, $translation);
            }
        }
        
        return $translation;
    }
}

