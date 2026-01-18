<?php

namespace App\Filament\Concerns;

trait EnvironmentalModuleGate
{
    protected static string $moduleKey = 'environmental';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.environmental', true);
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
