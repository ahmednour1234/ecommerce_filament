<?php

namespace App\Filament\Concerns;

trait ProductionModuleGate
{
    protected static string $moduleKey = 'production';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.production', true);
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
