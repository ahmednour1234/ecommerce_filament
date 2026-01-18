<?php

namespace App\Filament\Concerns;

trait SalesModuleGate
{
    protected static string $moduleKey = 'sales';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.sales', true);
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
