<?php

namespace App\Filament\Concerns;

trait PurchaseModuleGate
{
    protected static string $moduleKey = 'purchase';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.purchase', true);
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
