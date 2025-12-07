<?php

if (!function_exists('trans_dash')) {
    /**
     * Get dashboard translation
     */
    function trans_dash(string $key, ?string $default = null, ?string $languageCode = null): string
    {
        return app(\App\Services\MainCore\TranslationService::class)
            ->get($key, $languageCode, 'dashboard', $default ?? $key);
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

