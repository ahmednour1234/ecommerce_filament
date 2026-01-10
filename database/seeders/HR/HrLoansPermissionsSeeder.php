<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HrLoansPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating HR Loans module permissions...');

        $loanTypePermissions = [
            'hr.loan_types.view',
            'hr.loan_types.create',
            'hr.loan_types.update',
            'hr.loan_types.delete',
            'hr.loan_types.export',
        ];

        $loanPermissions = [
            'hr.loans.view',
            'hr.loans.create',
            'hr.loans.update',
            'hr.loans.delete',
            'hr.loans.export',
        ];

        $allPermissions = array_merge($loanTypePermissions, $loanPermissions);

        $permissions = [];
        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        $this->command->info('✓ HR Loans permissions created: ' . count($permissions));

        $this->createRolesAndAssignPermissions($permissions);
    }

    protected function createRolesAndAssignPermissions(array $permissions): void
    {
        $hrAdmin = Role::firstOrCreate(['name' => 'HR Admin', 'guard_name' => 'web']);
        $hrAdmin->syncPermissions($permissions);
        $this->command->info('✓ HR Admin role created/updated with all permissions');

        $hrOfficer = Role::firstOrCreate(['name' => 'HR Officer', 'guard_name' => 'web']);
        $hrOfficer->syncPermissions(array_filter($permissions, function ($permission) {
            return !str_contains($permission->name, '.delete');
        }));
        $this->command->info('✓ HR Officer role created/updated');

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerPermissions = array_filter($permissions, function ($permission) {
            return str_contains($permission->name, '.view') ||
                   str_contains($permission->name, '.export');
        });
        $manager->syncPermissions($managerPermissions);
        $this->command->info('✓ Manager role created/updated');
    }
}
