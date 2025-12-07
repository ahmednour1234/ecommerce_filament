<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // System Resources
        $systemResources = [
            'users',
            'roles',
            'permissions',
        ];

        // MainCore Resources
        $mainCoreResources = [
            'currencies',
            'currency_rates',
            'languages',
            'settings',
            'themes',
            'user_preferences',
            'payment_providers',
            'payment_methods',
            'payment_transactions',
            'notification_channels',
            'notification_templates',
            'shipping_providers',
            'shipments',
            'translations',
        ];

        // Combine all resources
        $allResources = array_merge($systemResources, $mainCoreResources);

        $perms = [];

        // Generate permissions for all resources
        foreach ($allResources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $perms[] = "{$resource}.{$action}";
            }
        }

        // Create all permissions
        foreach ($perms as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Create super_admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($perms);

        // Assign super_admin role to admin user
        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            $admin->assignRole($superAdmin);
        }
    }
}
