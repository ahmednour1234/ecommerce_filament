<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeCommissionPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Employee Commission permissions...');

        $permissions = [];

        $permissionGroups = [
            'hr_commission_types' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_commissions' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_employee_commission_tiers' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_employee_commission_assignments' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_employee_commission_report' => ['view', 'export', 'print'],
        ];

        foreach ($permissionGroups as $resource => $actions) {
            foreach ($actions as $action) {
                $permName = "{$resource}.{$action}";

                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web']
                );

                $permissions[] = $permission;
            }
        }

        $this->command->info('✓ Employee Commission permissions created: ' . count($permissions));

        // Assign to HR Manager role
        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        $hrManagerRole->givePermissionTo($permissions);
        $this->command->info('✓ All employee commission permissions assigned to HR Manager role');

        // Assign to super_admin role if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✓ All employee commission permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }
    }
}
