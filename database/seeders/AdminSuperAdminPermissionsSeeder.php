<?php

namespace Database\Seeders;

use App\Services\PermissionGrouper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSuperAdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Syncing all permissions to Admin and super_admin...');

        $permissions = Permission::where('guard_name', 'web')->get();
        $permissionNames = $permissions->pluck('name')->toArray();

        if (empty($permissionNames)) {
            $this->command->warn('No permissions found in database. Run module permission seeders first.');
            return;
        }

        foreach (['Admin', 'super_admin'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
            $this->command->info("✓ {$roleName}: synced " . count($permissionNames) . " permissions.");
        }

        PermissionGrouper::clearCache();
        $this->command->info('✅ AdminSuperAdminPermissionsSeeder finished.');
    }
}
