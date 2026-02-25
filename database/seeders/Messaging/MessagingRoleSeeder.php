<?php

namespace Database\Seeders\Messaging;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MessagingRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Messaging Manager role...');

        $role = Role::firstOrCreate([
            'name' => 'مدير قسم الرسائل',
            'guard_name' => 'web',
        ]);

        $messagingPermissions = Permission::whereIn('name', [
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
        ])->get();

        $role->syncPermissions($messagingPermissions);

        $this->command->info('✓ Messaging Manager role created with all messaging permissions');
    }
}
