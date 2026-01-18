<?php

namespace App\Filament\Concerns;

trait IntegrationsModuleGate
{
    protected static string $moduleKey = 'integrations';

    protected static function moduleEnabled(): bool
    {
        return (bool) config('modules.integrations', true);
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
