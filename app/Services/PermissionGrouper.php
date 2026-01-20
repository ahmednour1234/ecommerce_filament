<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionGrouper
{
    protected static ?array $cachedGroups = null;

    protected static array $actionPriority = [
        'view_any' => 1,
        'view' => 2,
        'create' => 3,
        'update' => 4,
        'delete' => 5,
        'restore' => 6,
        'force_delete' => 7,
        'export' => 8,
        'reconcile' => 9,
    ];

    public static function getGroupedPermissions(): array
    {
        if (self::$cachedGroups !== null) {
            return self::$cachedGroups;
        }

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $grouped = [];

        foreach ($permissions as $permission) {
            $module = self::extractModule($permission->name);
            $action = self::extractAction($permission->name);

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][$permission->id] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'priority' => self::getActionPriority($action),
            ];
        }

        foreach ($grouped as $module => $perms) {
            uasort($grouped[$module], function ($a, $b) {
                if ($a['priority'] !== $b['priority']) {
                    return $a['priority'] <=> $b['priority'];
                }
                return strcmp($a['name'], $b['name']);
            });
        }

        ksort($grouped);

        self::$cachedGroups = $grouped;

        return $grouped;
    }

    public static function extractModule(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        return $parts[0] ?? $permissionName;
    }

    public static function extractAction(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        if (count($parts) < 2) {
            return '';
        }
        return implode('.', array_slice($parts, 1));
    }

    protected static function getActionPriority(string $action): int
    {
        if (isset(self::$actionPriority[$action])) {
            return self::$actionPriority[$action];
        }
        return 999;
    }

    public static function getModules(): array
    {
        $grouped = self::getGroupedPermissions();
        return array_keys($grouped);
    }

    public static function getModulePermissions(string $module): array
    {
        $grouped = self::getGroupedPermissions();
        return $grouped[$module] ?? [];
    }

    public static function clearCache(): void
    {
        self::$cachedGroups = null;
    }
}
