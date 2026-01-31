<?php

namespace Database\Seeders\Packages;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PackagesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Packages module permissions...');

        $permissions = [
            'packages.view_any',
            'packages.view',
            'packages.create',
            'packages.update',
            'packages.delete',
            'packages.restore',
            'packages.force_delete',
            'packages.export_pdf',
        ];

        $createdPermissions = [];

        foreach ($permissions as $permName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $createdPermissions[] = $permission;
        }

        $this->command->info('✓ Packages permissions created: ' . count($createdPermissions));

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($createdPermissions);
        $this->command->info('✓ All packages permissions assigned to Admin role');

        $this->command->info('Note: Permission assignment to super_admin role is handled by SuperAdminSeeder');
    }
}
