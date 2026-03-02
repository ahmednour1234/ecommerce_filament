<?php

namespace Database\Seeders\Recruitment;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RecruitmentRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Recruitment Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير الاستقدام', 'guard_name' => 'web']);

        $permissions = [];
        $resources = ['agents', 'agent_prices', 'nationalities', 'professions', 'laborers', 'laborers_used'];

        foreach ($resources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $permissions[] = "recruitment.{$resource}.{$action}";
            }
        }

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Recruitment Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All recruitment permissions assigned to Super Admin role');
        }
    }
}
