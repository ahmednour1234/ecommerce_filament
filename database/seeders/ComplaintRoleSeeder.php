<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComplaintRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Complaints Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير الشكاوى', 'guard_name' => 'web']);

        $permissions = [
            'complaints.view_any',
            'complaints.view',
            'complaints.create',
            'complaints.update',
            'complaints.delete',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Complaints Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All complaints permissions assigned to Super Admin role');
        }
    }
}
