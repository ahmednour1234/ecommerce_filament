<?php

use App\Services\MainCore\SettingsService;
use App\Services\MainCore\CurrencyService;
use App\Services\MainCore\ThemeService;
use App\Services\MainCore\LocaleService;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        /** @var SettingsService $service */
        $service = app(SettingsService::class);

        return $service->get($key, $default);
    }
}

if (! function_exists('mc_settings')) {
    function mc_settings(): array
    {
        /** @var SettingsService $service */
        $service = app(SettingsService::class);

        return $service->all();
    }
}

if (! function_exists('mc_currency_format')) {
    function mc_currency_format(float $amount, ?string $code = null, bool $withSymbol = true): string
    {
        /** @var CurrencyService $service */
        $service = app(CurrencyService::class);

        return $service->format($amount, $code, null, $withSymbol);
    }
}

if (! function_exists('mc_currency_convert')) {
    function mc_currency_convert(float $amount, string $from, string $to): float
    {
        /** @var CurrencyService $service */
        $service = app(CurrencyService::class);

        return $service->convert($amount, $from, $to);
    }
}

if (! function_exists('mc_theme_color')) {
    function mc_theme_color(string $key, ?string $default = null): ?string
    {
        /** @var ThemeService $service */
        $service = app(ThemeService::class);

        return $service->color($key, $default);
    }
}

if (! function_exists('mc_logo')) {
    function mc_logo(string $variant = 'light'): ?string
    {
        /** @var ThemeService $service */
        $service = app(ThemeService::class);

        return $service->logo($variant);
    }
}

if (! function_exists('mc_lang')) {
    function mc_lang(): string
    {
        /** @var LocaleService $service */
        $service = app(LocaleService::class);

        return $service->currentLanguageCode();
    }
}

if (! function_exists('mc_timezone')) {
    function mc_timezone(): string
    {
        /** @var LocaleService $service */
        $service = app(LocaleService::class);

        return $service->currentTimezone();
    }
}
