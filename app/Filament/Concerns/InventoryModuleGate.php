<?php

namespace App\Filament\Concerns;

trait InventoryModuleGate
{
    protected static string $moduleKey = 'inventory';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.inventory', true);
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
