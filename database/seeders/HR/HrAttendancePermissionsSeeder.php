<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class HrAttendancePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR Attendance module permissions.
     */
    public function run(): void
    {
        $this->command->info('Creating HR Attendance module permissions...');

        // Work Places Permissions
        $workPlacePermissions = [
            'hr_work_places.view_any',
            'hr_work_places.view',
            'hr_work_places.create',
            'hr_work_places.update',
            'hr_work_places.delete',
        ];

        // Employee Groups Permissions
        $employeeGroupPermissions = [
            'hr_employee_groups.view_any',
            'hr_employee_groups.view',
            'hr_employee_groups.create',
            'hr_employee_groups.update',
            'hr_employee_groups.delete',
        ];

        // Assign Work Places Permissions
        $assignWorkPlacePermissions = [
            'hr_assign_work_places.view',
            'hr_assign_work_places.update',
        ];

        // Work Schedules Permissions
        $workSchedulePermissions = [
            'hr_work_schedules.view_any',
            'hr_work_schedules.view',
            'hr_work_schedules.create',
            'hr_work_schedules.update',
            'hr_work_schedules.delete',
        ];

        // Schedule Copy Permission
        $scheduleCopyPermissions = [
            'hr_schedule_copy.create',
        ];

        // Daily Attendance Permissions
        $dailyAttendancePermissions = [
            'hr_attendance_daily.view',
        ];

        // Monthly Report Permissions
        $monthlyReportPermissions = [
            'hr_attendance_report_monthly.view',
        ];

        // Excuse Requests Permissions
        $excuseRequestPermissions = [
            'hr_excuse_requests.view_any',
            'hr_excuse_requests.view',
            'hr_excuse_requests.create',
            'hr_excuse_requests.update',
            'hr_excuse_requests.delete',
            'hr_excuse_requests.approve',
            'hr_excuse_requests.reject',
        ];

        // Device Permissions
        $devicePermissions = [
            'hr_devices.view_any',
            'hr_devices.view',
            'hr_devices.create',
            'hr_devices.update',
            'hr_devices.delete',
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $workPlacePermissions,
            $employeeGroupPermissions,
            $assignWorkPlacePermissions,
            $workSchedulePermissions,
            $scheduleCopyPermissions,
            $dailyAttendancePermissions,
            $monthlyReportPermissions,
            $excuseRequestPermissions,
            $devicePermissions
        );

        // Create permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $this->command->info('✓ HR Attendance permissions created: ' . count($allPermissions));
        
        // Note: Permission assignment to super_admin role is handled by SuperAdminSeeder
        // which automatically syncs all permissions to super_admin role
    }
}

