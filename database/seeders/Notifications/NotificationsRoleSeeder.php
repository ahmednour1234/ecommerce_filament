<?php

namespace Database\Seeders\Notifications;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NotificationsRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Notifications Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير التنبيهات', 'guard_name' => 'web']);

        $permissions = [
            'notification_channels.view_any',
            'notification_channels.view',
            'notification_channels.create',
            'notification_channels.update',
            'notification_channels.delete',
            'notification_templates.view_any',
            'notification_templates.view',
            'notification_templates.create',
            'notification_templates.update',
            'notification_templates.delete',
            'recruitment_contracts.view_any',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Notifications Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All notifications permissions assigned to Super Admin role');
        }
    }
}
