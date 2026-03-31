<?php

namespace Database\Seeders\Rental;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RunRentalRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Rental Section Manager role...');

        $permissions = [
            'rental.contracts.view_any',
            'rental.contracts.view',
            'rental.contracts.create',
            'rental.contracts.update',
            'rental.contracts.delete',
            'rental.contracts.restore',
            'rental.contracts.force_delete',
            'rental.requests.view_any',
            'rental.requests.view',
            'rental.requests.manage',
            'rental.requests.convert',
            'rental.cancel_refund.view_any',
            'rental.cancel_refund.view',
            'rental.cancel_refund.manage',
            'rental.payments.view_any',
            'rental.payments.view',
            'rental.payments.create',
            'rental.payments.refund',
            'rental.print.contract',
            'rental.print.invoice',
            'rental.reports.view',
        ];

        // Ensure all permissions exist
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Create role and sync permissions
        $role = Role::firstOrCreate(['name' => 'مدير قسم التأجير', 'guard_name' => 'web']);
        $permissionModels = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($permissionModels);

        $this->command->info('✓ Role "مدير قسم التأجير" created with ' . $permissionModels->count() . ' permissions');

        // Assign to super_admin role if exists
        $superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissionModels);
            $this->command->info('✓ All rental permissions assigned to ' . $superAdmin->name . ' role');
        } else {
            $this->command->info('ℹ super_admin role not found. Skipping.');
        }

        $this->command->info('✓ Done! Assign the role via Admin → Users → Edit user → Roles.');
    }
}
