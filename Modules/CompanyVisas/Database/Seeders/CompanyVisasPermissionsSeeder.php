<?php

namespace Modules\CompanyVisas\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanyVisasPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Company Visas permissions...');

        $permissions = [];

        $permissionGroups = [
            'requests' => ['view_requests', 'create_requests', 'update_requests', 'delete_requests'],
            'contracts' => ['view_contracts', 'create_contracts', 'update_contracts', 'delete_contracts'],
        ];

        foreach ($permissionGroups as $group => $actions) {
            foreach ($actions as $action) {
                $permName = "company_visas.{$group}.{$action}";

                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web']
                );

                $permissions[] = $permission;
            }
        }

        $actionPermissions = ['link_workers', 'add_expense', 'update_status', 'manage_cost', 'manage_documents'];
        foreach ($actionPermissions as $action) {
            $permName = "company_visas.{$action}";
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }

        $this->command->info('✓ Company Visas permissions created: ' . count($permissions));

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All permissions assigned to Admin role');

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo($permissions);
        $this->command->info('✓ All permissions assigned to super_admin role');

        $financeRole = Role::firstOrCreate(['name' => 'Finance', 'guard_name' => 'web']);
        $financePermissions = array_filter([
            Permission::where('name', 'company_visas.requests.view_requests')->first(),
            Permission::where('name', 'company_visas.contracts.view_contracts')->first(),
            Permission::where('name', 'company_visas.add_expense')->first(),
            Permission::where('name', 'company_visas.manage_cost')->first(),
        ]);
        if (!empty($financePermissions)) {
            $financeRole->givePermissionTo($financePermissions);
            $this->command->info('✓ Finance permissions assigned');
        }

        $hrRole = Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
        $hrPermissions = array_filter([
            Permission::where('name', 'company_visas.requests.view_requests')->first(),
            Permission::where('name', 'company_visas.contracts.view_contracts')->first(),
            Permission::where('name', 'company_visas.link_workers')->first(),
            Permission::where('name', 'company_visas.update_status')->first(),
        ]);
        if (!empty($hrPermissions)) {
            $hrRole->givePermissionTo($hrPermissions);
            $this->command->info('✓ HR permissions assigned');
        }
    }
}
