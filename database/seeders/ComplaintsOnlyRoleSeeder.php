<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComplaintsOnlyRoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'مشرف قسم الشكاوي', 'guard_name' => 'web']);

        $permissions = [
            'complaints.view_any',
            'complaints.view',
            'complaints.create',
            'complaints.update',
            'complaints.delete',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);
    }
}
