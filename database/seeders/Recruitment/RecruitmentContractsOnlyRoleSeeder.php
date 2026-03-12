<?php

namespace Database\Seeders\Recruitment;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RecruitmentContractsOnlyRoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'مشرف عقود الاستقدام', 'guard_name' => 'web']);

        $permissions = [
            'recruitment_contracts.view_any',
            'recruitment_contracts.view',
            'recruitment_contracts.create',
            'recruitment_contracts.update',
            'recruitment_contracts.delete',
            'recruitment_contracts.status.update',
            'recruitment_contracts.assign_employee_branch',
            'view_receiving_recruitment_report',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);
    }
}
