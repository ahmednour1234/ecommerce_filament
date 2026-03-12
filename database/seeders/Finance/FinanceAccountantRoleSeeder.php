<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FinanceAccountantRoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'محاسب', 'guard_name' => 'web']);

        $permissions = [
            'finance.view_types',
            'finance.view_transactions',
            'finance.create_transactions',
            'finance.update_transactions',
            'finance.approve_transactions',
            'finance.reject_transactions',
            'finance.view_reports',
            'finance_reports.view',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);
    }
}
