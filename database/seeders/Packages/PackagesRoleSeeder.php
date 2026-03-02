<?php

namespace Database\Seeders\Packages;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PackagesRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Packages Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير باقات العروض', 'guard_name' => 'web']);

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

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Packages Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All packages permissions assigned to Super Admin role');
        }
    }
}
