<?php

namespace Database\Seeders\Recruitment;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RecruitmentPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Recruitment module permissions...');

        $resources = ['agents', 'agent_prices', 'nationalities', 'professions', 'laborers', 'laborers_used'];

        $permissions = [];

        foreach ($resources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $permName = "recruitment.{$resource}.{$action}";

                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web']
                );

                $permissions[] = $permission;
            }
        }

        $this->command->info('✓ Recruitment permissions created: ' . count($permissions));

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All recruitment permissions assigned to Admin role');

        // Note: Permission assignment to super_admin role is handled by SuperAdminSeeder
        // which automatically syncs all permissions to super_admin role
    }
}
