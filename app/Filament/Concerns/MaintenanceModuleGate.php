<?php

namespace App\Filament\Concerns;

trait MaintenanceModuleGate
{
    protected static string $moduleKey = 'maintenance';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.maintenance', true);
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
