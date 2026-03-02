<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HrRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating HR Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير الموارد البشرية', 'guard_name' => 'web']);

        $permissions = [];
        $hrResources = [
            'hr_departments',
            'hr_positions',
            'hr_blood_types',
            'hr_identity_types',
            'hr_banks',
            'hr_employees',
            'hr_holidays',
        ];

        foreach ($hrResources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $permissions[] = "{$resource}.{$action}";
            }
        }

        $permissions[] = 'hr_holidays.calendar';

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ HR Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All HR permissions assigned to Super Admin role');
        }
    }
}
