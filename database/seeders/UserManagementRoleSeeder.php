<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating user management role...');

        $permissions = [
            'users.view_any',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view_any',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view_any',
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
        ];

        $permissionModels = collect($permissions)->map(function (string $permissionName) {
            return Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        });

        $role = Role::firstOrCreate([
            'name' => 'ادارة المستخدمين',
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissionModels);

        $this->command->info('✓ User management role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ User management permissions assigned to Super Admin role');
        }
    }
}
