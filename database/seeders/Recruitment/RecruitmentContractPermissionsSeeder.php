<?php

namespace Database\Seeders\Recruitment;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RecruitmentContractPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Recruitment Contracts permissions...');

        $permissions = [];

        $actions = ['view_any', 'view', 'create', 'update', 'delete'];
        foreach ($actions as $action) {
            $permName = "recruitment_contracts.{$action}";

            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $specialPermissions = [
            'recruitment_contracts.finance.manage',
            'recruitment_contracts.status.update',
            'view_receiving_recruitment_report',
        ];

        foreach ($specialPermissions as $permName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $this->command->info('✓ Recruitment Contracts permissions created: ' . count($permissions));

        // Assign to Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All recruitment contracts permissions assigned to Admin role');

        // Assign to super_admin role if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✓ All recruitment contracts permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }
    }
}
