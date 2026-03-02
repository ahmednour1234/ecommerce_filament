<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FinanceSectionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Finance Section Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير قسم الحسابات', 'guard_name' => 'web']);

        $permissions = [
            'finance.view_types',
            'finance.manage_types',
            'finance.view_transactions',
            'finance.create_transactions',
            'finance.update_transactions',
            'finance.delete_transactions',
            'finance.approve_transactions',
            'finance.reject_transactions',
            'finance.transactions.import',
            'finance.view_reports',
            'finance.view_any',
            'finance.view_all_branches',
            'finance_reports.view',
            'finance_reports.export',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Finance Section Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All finance section permissions assigned to Super Admin role');
        }
    }
}
