<?php

namespace Database\Seeders\ServiceTransfer;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ServiceTransferRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Service Transfer Manager role...');

        $role = Role::firstOrCreate(['name' => 'مدير نقل الخدمات', 'guard_name' => 'web']);

        $permissions = [
            'service_transfer.view',
            'service_transfer.create',
            'service_transfer.update',
            'service_transfer.delete',
            'service_transfer.archive',
            'service_transfer.refund',
            'service_transfer.payments.create',
            'service_transfer.payments.view',
            'service_transfer.payments.delete',
            'service_transfers.documents.upload',
            'service_transfers.documents.delete',
            'service_transfers.print',
            'service_transfer.reports.view',
            'service_transfer.reports.export',
        ];

        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Service Transfer Manager role created with ' . $permissionModels->count() . ' permissions');

        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All service transfer permissions assigned to Super Admin role');
        }
    }
}
