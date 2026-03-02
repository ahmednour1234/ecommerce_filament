<?php

namespace Database\Seeders\Clients;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClientsRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Clients Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير العملاء', 'guard_name' => 'web']);

        $permissions = [
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Clients Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All clients permissions assigned to Super Admin role');
        }
    }
}
