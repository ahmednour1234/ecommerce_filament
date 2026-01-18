<?php

namespace App\Filament\Concerns;

trait SecurityModuleGate
{
    protected static string $moduleKey = 'security';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.security', true);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::moduleEnabled() && static::canViewAny();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return static::moduleEnabled() && static::canViewAny();
    }
}
