<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HrAlertsRoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'مشرف تنبيهات الموارد البشرية', 'guard_name' => 'web']);

        $permissionNames = [
            'hr.leave_requests.view_any',
            'hr.leave_requests.approve',
            'hr.leave_requests.reject',
            'hr_excuse_requests.view_any',
            'hr_excuse_requests.approve',
            'hr_excuse_requests.reject',
        ];

        $permissionModels = collect($permissionNames)->map(fn ($name) => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']))->all();
        $role->syncPermissions($permissionModels);
    }
}
