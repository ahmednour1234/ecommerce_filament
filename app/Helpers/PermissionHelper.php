<?php

namespace App\Helpers;

use App\Services\PermissionGrouper;

class PermissionHelper
{
    public static function getPermissionLabel(string $permissionKey, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        $fullKey = "permissions.labels.{$permissionKey}";
        $translation = trans($fullKey, [], $locale);

        if ($translation !== $fullKey) {
            return $translation;
        }

        $module = PermissionGrouper::extractModule($permissionKey);
        $action = PermissionGrouper::extractAction($permissionKey);

        if ($module && $action) {
            $moduleLabel = trans("permissions.modules.{$module}", [], $locale);
            $actionLabel = trans("permissions.actions.{$action}", [], $locale);

            if ($moduleLabel !== "permissions.modules.{$module}" && $actionLabel !== "permissions.actions.{$action}") {
                return "{$moduleLabel} - {$actionLabel}";
            }
        }

        return $permissionKey;
    }

    public static function getModuleLabel(string $module, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $key = "permissions.modules.{$module}";
        $translation = trans($key, [], $locale);

        return $translation !== $key ? $translation : $module;
    }

    public static function getActionLabel(string $action, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $key = "permissions.actions.{$action}";
        $translation = trans($key, [], $locale);

        return $translation !== $key ? $translation : $action;
    }
}
