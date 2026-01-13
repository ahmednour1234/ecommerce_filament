<?php

namespace App\Filament\Concerns;

trait HasModuleToggle
{
    protected static string $moduleKey = '';

    protected static function isModuleEnabled(): bool
    {
        if (static::$moduleKey === '') {
            return true;
        }

        return (bool) config('modules.' . static::$moduleKey, true);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // يخفي من السايدبار
        return static::isModuleEnabled();
    }

    public static function canAccess(): bool
    {
        // يمنع الدخول بالرابط المباشر
        return static::isModuleEnabled();
    }
}
