<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccountingPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Journal Entry Permissions
        $journalEntryPermissions = [
            'journal_entries.view_any',
            'journal_entries.view',
            'journal_entries.create',
            'journal_entries.update',
            'journal_entries.delete',
            'journal_entries.submit',
            'journal_entries.approve',
            'journal_entries.reject',
            'journal_entries.post',
        ];

        // Account Permissions
        $accountPermissions = [
            'accounts.view_any',
            'accounts.view',
            'accounts.create',
            'accounts.update',
            'accounts.delete',
        ];

        // Journal Permissions
        $journalPermissions = [
            'journals.view_any',
            'journals.view',
            'journals.create',
            'journals.update',
            'journals.delete',
        ];

        // Voucher Permissions
        $voucherPermissions = [
            'vouchers.view_any',
            'vouchers.view',
            'vouchers.create',
            'vouchers.update',
            'vouchers.delete',
            'vouchers.approve',
            'vouchers.post',
        ];

        // Fiscal Year Permissions
        $fiscalYearPermissions = [
            'fiscal_years.view_any',
            'fiscal_years.view',
            'fiscal_years.create',
            'fiscal_years.update',
            'fiscal_years.delete',
            'fiscal_years.close',
        ];

        // Period Permissions
        $periodPermissions = [
            'periods.view_any',
            'periods.view',
            'periods.create',
            'periods.update',
            'periods.delete',
            'periods.close',
        ];

        // Project Permissions
        $projectPermissions = [
            'projects.view_any',
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
        ];

        // Report Permissions
        $reportPermissions = [
            'reports.trial_balance',
            'reports.general_ledger',
            'reports.account_statement',
            'reports.income_statement',
            'reports.balance_sheet',
            'reports.cash_flow',
        ];

        // Bank Account Permissions
        $bankAccountPermissions = [
            'bank_accounts.view_any',
            'bank_accounts.view',
            'bank_accounts.create',
            'bank_accounts.update',
            'bank_accounts.delete',
            'bank_accounts.reconcile',
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $journalEntryPermissions,
            $accountPermissions,
            $journalPermissions,
            $voucherPermissions,
            $fiscalYearPermissions,
            $periodPermissions,
            $projectPermissions,
            $reportPermissions,
            $bankAccountPermissions
        );

        // Create permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles (if roles exist)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($allPermissions);
        }

        $accountantRole = Role::where('name', 'accountant')->first();
        if ($accountantRole) {
            $accountantRole->givePermissionTo(array_merge(
                $journalEntryPermissions,
                $accountPermissions,
                $voucherPermissions,
                $projectPermissions,
                $reportPermissions
            ));
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo(array_merge(
                $journalEntryPermissions,
                $voucherPermissions,
                $reportPermissions
            ));
        }
    }
}

