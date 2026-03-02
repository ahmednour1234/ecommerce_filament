<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployeeCommissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Employee Commission Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير عمولات الموظفين', 'guard_name' => 'web']);

        $permissions = [];
        $permissionGroups = [
            'hr_commission_types' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_commissions' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_employee_commission_tiers' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_employee_commission_assignments' => ['view_any', 'view', 'create', 'update', 'delete', 'restore'],
            'hr_employee_commission_report' => ['view', 'export', 'print'],
        ];

        foreach ($permissionGroups as $resource => $actions) {
            foreach ($actions as $action) {
                $permissions[] = "{$resource}.{$action}";
            }
        }

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Employee Commission Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All employee commission permissions assigned to Super Admin role');
        }
    }
}
