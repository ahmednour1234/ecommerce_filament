<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HrGeneralAndBranchManagerRoleSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'hr.view_all_branches', 'guard_name' => 'web']);

        $hrPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', 'hr.%')->orWhere('name', 'like', 'hr_%');
        })->get();

        $generalManager = Role::firstOrCreate(['name' => 'مدير عام موارد بشرية', 'guard_name' => 'web']);
        $generalManager->syncPermissions($hrPermissions);

        $branchManager = Role::firstOrCreate(['name' => 'مدير فرع موارد بشرية', 'guard_name' => 'web']);
        $branchManager->syncPermissions($hrPermissions->filter(fn ($p) => $p->name !== 'hr.view_all_branches'));
    }
}
