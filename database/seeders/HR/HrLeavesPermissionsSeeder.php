<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HrLeavesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR Leaves module permissions and roles.
     */
    public function run(): void
    {
        $this->command->info('Creating HR Leaves module permissions...');

        // Leave Types Permissions
        $leaveTypePermissions = [
            'hr.leave_types.view',
            'hr.leave_types.create',
            'hr.leave_types.update',
            'hr.leave_types.delete',
            'hr.leave_types.export',
        ];

        // Leave Requests Permissions
        $leaveRequestPermissions = [
            'hr.leave_requests.view_any',
            'hr.leave_requests.view_own',
            'hr.leave_requests.create',
            'hr.leave_requests.update',
            'hr.leave_requests.approve',
            'hr.leave_requests.reject',
            'hr.leave_requests.cancel',
            'hr.leave_requests.delete',
            'hr.leave_requests.export',
        ];

        // Leave Balance Permissions
        $leaveBalancePermissions = [
            'hr.leave_balance.view',
            'hr.leave_balance.recalculate',
            'hr.leave_balance.export',
        ];

        // Leave Reports Permissions
        $leaveReportPermissions = [
            'hr.leave_reports.view',
            'hr.leave_reports.export',
        ];

        $allPermissions = array_merge(
            $leaveTypePermissions,
            $leaveRequestPermissions,
            $leaveBalancePermissions,
            $leaveReportPermissions
        );

        $permissions = [];
        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        $this->command->info('✓ HR Leaves permissions created: ' . count($permissions));

        // Create roles and assign permissions
        $this->createRolesAndAssignPermissions($permissions);

        // Note: Permission assignment to super_admin role is handled by SuperAdminSeeder
        // which automatically syncs all permissions to super_admin role
    }

    /**
     * Create roles and assign permissions
     */
    protected function createRolesAndAssignPermissions(array $permissions): void
    {
        // HR Admin - All permissions
        $hrAdmin = Role::firstOrCreate(['name' => 'HR Admin', 'guard_name' => 'web']);
        $hrAdmin->syncPermissions($permissions);
        $this->command->info('✓ HR Admin role created/updated with all permissions');

        // HR Officer - Most permissions except delete
        $hrOfficer = Role::firstOrCreate(['name' => 'HR Officer', 'guard_name' => 'web']);
        $hrOfficer->syncPermissions(array_filter($permissions, function ($permission) {
            return !str_contains($permission->name, '.delete');
        }));
        $this->command->info('✓ HR Officer role created/updated');

        // Manager - Approve/reject/view team permissions
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerPermissions = array_filter($permissions, function ($permission) {
            return str_contains($permission->name, '.view') ||
                   str_contains($permission->name, '.approve') ||
                   str_contains($permission->name, '.reject') ||
                   str_contains($permission->name, '.export');
        });
        $manager->syncPermissions($managerPermissions);
        $this->command->info('✓ Manager role created/updated');

        // Employee - Create/view own permissions
        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employeePermissions = array_filter($permissions, function ($permission) {
            return str_contains($permission->name, '.view_own') ||
                   str_contains($permission->name, '.create') ||
                   str_contains($permission->name, '.cancel');
        });
        $employee->syncPermissions($employeePermissions);
        $this->command->info('✓ Employee role created/updated');
    }
}

