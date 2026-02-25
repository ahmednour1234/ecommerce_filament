<?php

namespace Database\Seeders\Housing;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HousingRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Housing Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير قسم الإيواء', 'guard_name' => 'web']);

        $permissions = Permission::where('name', 'like', 'housing.%')->get();
        $role->syncPermissions($permissions);

        $this->command->info('✓ Housing Manager role created with ' . $permissions->count() . ' permissions');

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo($permissions);
        $this->command->info('✓ All housing permissions assigned to Super Admin role');
    }
}
