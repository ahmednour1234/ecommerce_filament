<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PayrollPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating HR Payroll module permissions...');

        $permissions = [];

        $componentsActions = ['view_any', 'view', 'create', 'update', 'delete'];
        foreach ($componentsActions as $action) {
            $permName = "hr_components.{$action}";
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        $payrollActions = ['view_any', 'view', 'create', 'approve', 'pay', 'export'];
        foreach ($payrollActions as $action) {
            $permName = "hr_payroll.{$action}";
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        $financialActions = ['view_any', 'view', 'update'];
        foreach ($financialActions as $action) {
            $permName = "hr_employee_financial.{$action}";
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        $this->command->info('✓ HR Payroll permissions created: ' . count($permissions));
    }
}
