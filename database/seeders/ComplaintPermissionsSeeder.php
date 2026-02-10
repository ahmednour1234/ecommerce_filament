<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ComplaintPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Complaints permissions...');

        $permissions = [];

        $actions = ['view_any', 'view', 'create', 'update', 'delete'];
        foreach ($actions as $action) {
            $permName = "complaints.{$action}";

            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $this->command->info('✓ Complaints permissions created: ' . count($permissions));

        // Assign to Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All complaints permissions assigned to Admin role');

        // Assign to super_admin role if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✓ All complaints permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }
    }
}
