<?php

namespace Database\Seeders\Messaging;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MessagingPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Messaging module permissions...');

        $permissions = [];

        $permissionList = [
            'view_any_message_contacts',
            'create_message_contacts',
            'update_message_contacts',
            'delete_message_contacts',
            'restore_message_contacts',
            'view_any_sms_messages',
            'view_sms_messages',
            'delete_sms_messages',
            'restore_sms_messages',
            'export_sms_messages',
            'print_sms_messages',
            'view_any_contact_messages',
            'view_contact_messages',
            'update_contact_messages',
            'delete_contact_messages',
            'restore_contact_messages',
            'export_contact_messages',
            'print_contact_messages',
            'manage_sms_templates',
            'manage_sms_settings',
            'send_sms',
        ];

        foreach ($permissionList as $permName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissions[] = $permission;
        }

        $this->command->info('✓ Messaging permissions created: ' . count($permissions));

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
        $this->command->info('✓ All messaging permissions assigned to Admin role');

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✓ All messaging permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }
    }
}
