<?php

namespace App\Filament\Concerns;

trait NotificationModuleGate
{
    protected static string $moduleKey = 'notification';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.notification', true);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::moduleEnabled() && static::canViewAny();
    }

    public static function canAccess(): bool
    {
        return static::moduleEnabled() && static::canViewAny();
    }
}
