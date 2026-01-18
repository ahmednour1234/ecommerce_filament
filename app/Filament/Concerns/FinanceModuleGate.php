<?php

namespace App\Filament\Concerns;

trait FinanceModuleGate
{
    protected static string $moduleKey = 'finance';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.finance', true);
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
