<?php

namespace App\Filament\Concerns;

trait HrModuleGate
{
    protected static string $moduleKey = 'hr';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.hr', true);
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
