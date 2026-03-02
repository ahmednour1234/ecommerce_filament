<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccountingRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Accounting Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير المحاسبة', 'guard_name' => 'web']);

        $permissions = [
            'journal_entries.view_any',
            'journal_entries.view',
            'journal_entries.create',
            'journal_entries.update',
            'journal_entries.delete',
            'journal_entries.submit',
            'journal_entries.approve',
            'journal_entries.reject',
            'journal_entries.post',
            'accounts.view_any',
            'accounts.view',
            'accounts.create',
            'accounts.update',
            'accounts.delete',
            'journals.view_any',
            'journals.view',
            'journals.create',
            'journals.update',
            'journals.delete',
            'vouchers.view_any',
            'vouchers.view',
            'vouchers.create',
            'vouchers.update',
            'vouchers.delete',
            'vouchers.approve',
            'vouchers.post',
            'fiscal_years.view_any',
            'fiscal_years.view',
            'fiscal_years.create',
            'fiscal_years.update',
            'fiscal_years.delete',
            'fiscal_years.close',
            'periods.view_any',
            'periods.view',
            'periods.create',
            'periods.update',
            'periods.delete',
            'periods.close',
            'projects.view_any',
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'reports.trial_balance',
            'reports.general_ledger',
            'reports.account_statement',
            'reports.income_statement',
            'reports.balance_sheet',
            'reports.cash_flow',
            'bank_accounts.view_any',
            'bank_accounts.view',
            'bank_accounts.create',
            'bank_accounts.update',
            'bank_accounts.delete',
            'bank_accounts.reconcile',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Accounting Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All accounting permissions assigned to Super Admin role');
        }
    }
}
