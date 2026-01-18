<?php

namespace App\Filament\Concerns;

trait QualityModuleGate
{
    protected static string $moduleKey = 'quality';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.quality', true);
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
