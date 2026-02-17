<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ServiceTransferPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Service Transfer permissions...');

        $permissions = [];

        $actions = ['view', 'create', 'update', 'delete', 'archive', 'refund'];
        foreach ($actions as $action) {
            $permName = "service_transfer.{$action}";

            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $paymentActions = ['create', 'delete'];
        foreach ($paymentActions as $action) {
            $permName = "service_transfer.payments.{$action}";

            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $reportActions = ['view', 'export'];
        foreach ($reportActions as $action) {
            $permName = "service_transfer.reports.{$action}";

            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $this->command->info('✓ Service Transfer permissions created: ' . count($permissions));

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✓ All service transfer permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminPermissions = array_filter($permissions, function ($perm) {
            return $perm->name !== 'service_transfer.delete';
        });
        $adminRole->givePermissionTo($adminPermissions);
        $this->command->info('✓ Service transfer permissions (except delete) assigned to Admin role');

        $accountantRole = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountantPermissions = array_filter($permissions, function ($perm) {
            return in_array($perm->name, [
                'service_transfer.view',
                'service_transfer.payments.create',
                'service_transfer.payments.delete',
                'service_transfer.reports.view',
                'service_transfer.reports.export',
            ]);
        });
        $accountantRole->givePermissionTo($accountantPermissions);
        $this->command->info('✓ Service transfer view, payments, and reports permissions assigned to Accountant role');

        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewerPermissions = array_filter($permissions, function ($perm) {
            return $perm->name === 'service_transfer.view';
        });
        $viewerRole->givePermissionTo($viewerPermissions);
        $this->command->info('✓ Service transfer view permission assigned to Viewer role');
    }
}
