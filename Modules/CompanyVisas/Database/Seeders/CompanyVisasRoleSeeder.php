<?php

namespace Modules\CompanyVisas\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CompanyVisasRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Company Visas Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير تأشيرات الشركة', 'guard_name' => 'web']);

        $permissions = [
            'company_visas.view_requests',
            'company_visas.create_requests',
            'company_visas.update_requests',
            'company_visas.delete_requests',
            'company_visas.view_contracts',
            'company_visas.create_contracts',
            'company_visas.update_contracts',
            'company_visas.delete_contracts',
            'company_visas.link_workers',
            'company_visas.add_expense',
            'company_visas.update_status',
            'company_visas.manage_cost',
            'company_visas.manage_documents',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Company Visas Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All company visas permissions assigned to Super Admin role');
        }
    }
}
