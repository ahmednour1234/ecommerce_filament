<?php

namespace Database\Seeders\Recruitment;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RecruitmentContractRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Recruitment Contracts Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير عقود الاستقدام', 'guard_name' => 'web']);

        $permissions = [
            'recruitment_contracts.view_any',
            'recruitment_contracts.view',
            'recruitment_contracts.create',
            'recruitment_contracts.update',
            'recruitment_contracts.delete',
            'recruitment_contracts.finance.manage',
            'recruitment_contracts.status.update',
            'view_receiving_recruitment_report',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Recruitment Contracts Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All recruitment contracts permissions assigned to Super Admin role');
        }
    }
}
