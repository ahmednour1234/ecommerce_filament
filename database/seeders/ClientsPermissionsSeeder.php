<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClientsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Clients module permissions...');

        $permissions = [];

        foreach (['view', 'create', 'update', 'delete'] as $action) {
            $permName = "clients.{$action}";

            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $this->command->info('✓ Clients permissions created: ' . count($permissions));

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All clients permissions assigned to Admin role');

        // Note: Permission assignment to super_admin role is handled by SuperAdminSeeder
        // which automatically syncs all permissions to super_admin role
    }
}
