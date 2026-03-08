<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HrNotificationsRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating HR Notifications roles...');

        // Branch Manager Role
        $branchManagerRole = Role::firstOrCreate(['name' => 'مدير فرع', 'guard_name' => 'web']);
        
        $branchManagerPermissions = [
            'hr_notifications.view_any',
            'hr_notifications.view',
            'hr_notifications.view_branch',
        ];

        $branchManagerPermissionModels = Permission::whereIn('name', $branchManagerPermissions)->get();
        $branchManagerRole->syncPermissions($branchManagerPermissionModels);
        $this->command->info('✓ Branch Manager role created with ' . $branchManagerPermissionModels->count() . ' permissions');

        // General Manager Role
        $generalManagerRole = Role::firstOrCreate(['name' => 'مدير عام', 'guard_name' => 'web']);
        
        $generalManagerPermissions = [
            'hr_notifications.view_any',
            'hr_notifications.view',
            'hr_notifications.view_all',
        ];

        $generalManagerPermissionModels = Permission::whereIn('name', $generalManagerPermissions)->get();
        $generalManagerRole->syncPermissions($generalManagerPermissionModels);
        $this->command->info('✓ General Manager role created with ' . $generalManagerPermissionModels->count() . ' permissions');

        // Employee Role (for viewing own notifications)
        $employeeRole = Role::firstOrCreate(['name' => 'موظف', 'guard_name' => 'web']);
        
        $employeePermissions = [
            'hr_notifications.view_any',
            'hr_notifications.view',
            'hr_notifications.view_own',
        ];

        $employeePermissionModels = Permission::whereIn('name', $employeePermissions)->get();
        $employeeRole->syncPermissions($employeePermissionModels);
        $this->command->info('✓ Employee role created with ' . $employeePermissionModels->count() . ' permissions');
    }
}
