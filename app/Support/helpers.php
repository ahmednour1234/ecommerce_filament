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
     * Get menu translation with fallback: current locale → 'en' → default → key
     * 
     * @param string $key Translation key (e.g., 'menu.dashboard', 'menu.sales.customers')
     * @param string|null $default Default value if translation not found
     * @return string Translated text
     */
    function tr(string $key, ?string $default = null): string
    {
        return app(\App\Services\MainCore\TranslationService::class)
            ->get($key, null, 'menu', $default ?? $key);
    }
}

