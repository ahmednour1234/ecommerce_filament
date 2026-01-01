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
            'warehouses',
            'voucher_signatures', // ✅ ADD THIS

        ];

        // Catalog Resources
        $catalogResources = [
            'brands',
            'categories',
            'products',
            'batches',
        ];

        // Sales Resources
        $salesResources = [
            'customers',
            'orders',
            'order_items',
            'invoices',
            'invoice_items',
            'installments',
            'installment_payments',
        ];

        // Accounting Resources
        $accountingResources = [
            'accounts',
            'journals',
            'journal_entries',
            'journal_entry_lines',
            'vouchers',
            'voucher_signatures', // ✅ ADD THIS
            'assets',
            'journal_entries.post',
            'vouchers.create_journal_entry',
            'journal_entries.post',
            'vouchers.create_journal_entry',
            'vouchers.print',
            'vouchers.export_pdf',
            'vouchers.export_excel',
            'vouchers.print',              // ✅ optional
            'vouchers.select_signatures',  // ✅ optional
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

        // Note: super_admin role creation and permission assignment
        // is now handled by SuperAdminSeeder to ensure ALL permissions
        // (including accounting and any future modules) are included
    }
}
