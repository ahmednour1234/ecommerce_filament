<?php

namespace Database\Seeders\Housing;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HousingPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Housing permissions...');

        $permissions = [];

        $permissionGroups = [
            'dashboard' => ['view'],
            'requests' => ['view_any', 'view', 'create', 'update', 'delete'],
            'salaries' => ['view_any', 'create', 'update', 'delete'],
            'leaves' => ['view_any', 'create', 'update', 'delete'],
            'accommodation_entries' => ['view_any', 'create', 'update', 'delete'],
            'statuses' => ['view_any', 'view', 'create', 'update', 'delete'],
            'reports' => ['view'],
        ];

        foreach ($permissionGroups as $group => $actions) {
            foreach ($actions as $action) {
                $permName = "housing.{$group}.{$action}";

                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web']
                );

                $permissions[] = $permission;
            }
        }

        $this->command->info('✓ Housing permissions created: ' . count($permissions));

        // Assign to Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All housing permissions assigned to Admin role');

        // Assign to super_admin role if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✓ All housing permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }
    }
}
