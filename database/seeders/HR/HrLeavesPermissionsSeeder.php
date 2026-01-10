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

        // Leave Types Permissions (used by LeaveTypeResource)
        $leaveTypePermissions = [
            'hr.leave_types.view',        // Used in: LeaveTypeResource::canViewAny(), shouldRegisterNavigation()
            'hr.leave_types.create',      // Used in: LeaveTypeResource::canCreate()
            'hr.leave_types.update',      // Used in: LeaveTypeResource::canEdit(), EditAction visibility
            'hr.leave_types.delete',      // Used in: LeaveTypeResource::canDelete(), DeleteAction visibility
            'hr.leave_types.export',      // Used in: ListLeaveTypes header actions (Excel, PDF, Print)
        ];

        // Leave Requests Permissions (used by LeaveRequestResource)
        $leaveRequestPermissions = [
            'hr.leave_requests.view_any', // Used in: LeaveRequestResource::canViewAny()
            'hr.leave_requests.view_own', // Used in: LeaveRequestResource::shouldRegisterNavigation(), MyLeaveRequests page
            'hr.leave_requests.create',   // Used in: LeaveRequestResource::canCreate()
            'hr.leave_requests.update',   // Used in: LeaveRequestResource::canEdit(), EditAction visibility
            'hr.leave_requests.approve',  // Used in: LeaveRequestResource table actions (ApproveAction)
            'hr.leave_requests.reject',   // Used in: LeaveRequestResource table actions (RejectAction)
            'hr.leave_requests.cancel',   // Used in: LeaveRequestResource table actions (CancelAction)
            'hr.leave_requests.delete',   // Used in: LeaveRequestResource::canDelete(), DeleteAction visibility
            'hr.leave_requests.export',   // Used in: ListLeaveRequests header actions (Excel, PDF, Print)
        ];

        // Leave Balance Permissions (used by LeaveBalancePage)
        $leaveBalancePermissions = [
            'hr.leave_balance.view',      // Used in: LeaveBalancePage::shouldRegisterNavigation()
            'hr.leave_balance.recalculate', // Used in: LeaveBalancePage header actions (RecalculateAction)
            'hr.leave_balance.export',    // Used in: LeaveBalancePage header actions (Export actions)
        ];

        // Leave Reports Permissions (used by LeaveReportPage)
        $leaveReportPermissions = [
            'hr.leave_reports.view',      // Used in: LeaveReportPage::shouldRegisterNavigation()
            'hr.leave_reports.export',    // Used in: LeaveReportPage header actions (Export actions)
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

