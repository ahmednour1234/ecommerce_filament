<?php

namespace Database\Seeders\Rental;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RentalPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating Rental module permissions...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $this->command->info('Step 1: Creating Contracts permissions...');
        $contractPermissions = [
            'rental.contracts.view_any',
            'rental.contracts.view',
            'rental.contracts.create',
            'rental.contracts.update',
            'rental.contracts.delete',
            'rental.contracts.restore',
            'rental.contracts.force_delete',
        ];

        $this->command->info('Step 2: Creating Requests permissions...');
        $requestPermissions = [
            'rental.requests.view_any',
            'rental.requests.view',
            'rental.requests.manage',
            'rental.requests.convert',
        ];

        $this->command->info('Step 3: Creating Cancel/Refund permissions...');
        $cancelRefundPermissions = [
            'rental.cancel_refund.view_any',
            'rental.cancel_refund.view',
            'rental.cancel_refund.manage',
        ];

        $this->command->info('Step 4: Creating Payments permissions...');
        $paymentPermissions = [
            'rental.payments.view_any',
            'rental.payments.view',
            'rental.payments.create',
            'rental.payments.refund',
        ];

        $this->command->info('Step 5: Creating Print permissions...');
        $printPermissions = [
            'rental.print.contract',
            'rental.print.invoice',
        ];

        $this->command->info('Step 6: Creating Reports permissions...');
        $reportPermissions = [
            'rental.reports.view',
        ];

        $allPermissions = array_merge(
            $contractPermissions,
            $requestPermissions,
            $cancelRefundPermissions,
            $paymentPermissions,
            $printPermissions,
            $reportPermissions
        );

        $createdPermissions = [];

        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            $createdPermissions[] = $permission;
        }

        $this->command->info("✓ Rental permissions created: " . count($createdPermissions));
        $this->command->newLine();

        $this->command->info('Step 7: Assigning permissions to roles...');

        // Assign to Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($allPermissions);
        $this->command->info('✓ All rental permissions assigned to Admin role');

        // Assign to super_admin role if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
            $this->command->info('✓ All rental permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✓ Rental permissions seeder completed successfully!');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
