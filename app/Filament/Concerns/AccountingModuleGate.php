<?php

namespace App\Filament\Concerns;

trait AccountingModuleGate
{
    protected static string $moduleKey = 'accounting';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.accounting', true);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::moduleEnabled() && static::canViewAny();
    }

    public static function canAccess(): bool
    {
        return static::moduleEnabled() && static::canViewAny();
    }
}
