<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // أذونات أساسية لثلاث موارد مثالاً (users, roles, permissions)
        $resources = ['users', 'roles', 'permissions'];

        $perms = [];

        foreach ($resources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $perms[] = "{$resource}.{$action}";
            }
        }

        foreach ($perms as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // super_admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($perms);

        // اربط الـ admin user بالـ role ده
        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            $admin->assignRole($superAdmin);
        }
    }
}
