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
            'branches',
            'cost_centers',
        ];

        // Catalog Resources
        $catalogResources = [
            'brands',
            'categories',
            'products',
        ];

        // Sales Resources
        $salesResources = [
            'customers',
            'orders',
            'order_items',
            'invoices',
            'invoice_items',
        ];

        // Accounting Resources
        $accountingResources = [
            'accounts',
            'journals',
            'journal_entries',
            'journal_entry_lines',
            'vouchers',
        ];

        // Combine all resources
        $allResources = array_merge(
            $systemResources,
            $mainCoreResources,
            $catalogResources,
            $salesResources,
            $accountingResources
        );

        $permNames = [];
        $permissions = [];

        // Generate permissions for all resources
        foreach ($allResources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $permName = "{$resource}.{$action}";
                $permNames[] = $permName;

                // Create permission and collect the object
                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web']
                );
                $permissions[] = $permission;
            }
        }

        // Add special permissions for specific resources
        $specialPermissions = [
            'journal_entries.post', // Post journal entries
            'vouchers.create_journal_entry', // Create journal entry from voucher
        ];

        foreach ($specialPermissions as $permName) {
            $permNames[] = $permName;
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        // Create super_admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // Sync using Permission objects (more reliable than names)
        $superAdmin->syncPermissions($permissions);

        // Assign super_admin role to admin user
        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            $admin->assignRole($superAdmin);
        }
    }
}
