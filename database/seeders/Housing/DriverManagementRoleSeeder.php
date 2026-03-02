<?php

namespace Database\Seeders\Housing;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DriverManagementRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Driver Management role...');

        $role = Role::firstOrCreate(['name' => 'مدير إدارة السائقين', 'guard_name' => 'web']);

        $permissions = [
            'housing.drivers.view_any',
            'housing.drivers.view',
            'housing.drivers.create',
            'housing.drivers.update',
            'housing.drivers.delete',
            'housing.cars.view_any',
            'housing.cars.view',
            'housing.cars.create',
            'housing.cars.update',
            'housing.cars.delete',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Driver Management role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All driver management permissions assigned to Super Admin role');
        }
    }
}
