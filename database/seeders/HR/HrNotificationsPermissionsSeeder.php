<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class HrNotificationsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating HR Notifications permissions...');

        $permissions = [
            'hr_notifications.view_any',
            'hr_notifications.view',
            'hr_notifications.create',
            'hr_notifications.update',
            'hr_notifications.delete',
            'hr_notifications.view_all',
            'hr_notifications.view_branch',
            'hr_notifications.view_own',
        ];

        $createdPermissions = [];
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            $createdPermissions[] = $permission;
        }

        $this->command->info('✓ HR Notifications permissions created: ' . count($createdPermissions));

        $superAdmin = \Spatie\Permission\Models\Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($createdPermissions);
            $this->command->info('✓ All HR Notifications permissions assigned to Super Admin role');
        }
    }
}
