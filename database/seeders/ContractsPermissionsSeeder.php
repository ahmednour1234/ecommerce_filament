<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Contracts Permissions Seeder
 * 
 * This seeder creates permissions for both:
 * - Recruitment Contracts (عقود الاستقدام)
 * - Rental Contracts (عقود الإيجار)
 * 
 * Run: php artisan db:seed --class=ContractsPermissionsSeeder
 */
class ContractsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating Contracts Permissions (Recruitment & Rental)...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $allPermissions = [];

        // ============================================
        // Recruitment Contracts Permissions
        // ============================================
        $this->command->info('Step 1: Creating Recruitment Contracts permissions...');
        
        $recruitmentActions = ['view_any', 'view', 'create', 'update', 'delete'];
        $recruitmentPermissions = [];
        
        foreach ($recruitmentActions as $action) {
            $permName = "recruitment_contracts.{$action}";
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $recruitmentPermissions[] = $permission;
            $allPermissions[] = $permName;
        }

        $recruitmentSpecialPermissions = [
            'recruitment_contracts.finance.manage',
            'recruitment_contracts.status.update',
            'view_receiving_recruitment_report',
        ];

        foreach ($recruitmentSpecialPermissions as $permName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $recruitmentPermissions[] = $permission;
            $allPermissions[] = $permName;
        }

        $this->command->info("✓ Recruitment Contracts permissions created: " . count($recruitmentPermissions));
        $this->command->newLine();

        // ============================================
        // Rental Contracts Permissions
        // ============================================
        $this->command->info('Step 2: Creating Rental Contracts permissions...');
        
        $rentalContractPermissions = [
            'rental.contracts.view_any',
            'rental.contracts.view',
            'rental.contracts.create',
            'rental.contracts.update',
            'rental.contracts.delete',
            'rental.contracts.restore',
            'rental.contracts.force_delete',
        ];

        $rentalRequestPermissions = [
            'rental.requests.view_any',
            'rental.requests.view',
            'rental.requests.manage',
            'rental.requests.convert',
        ];

        $rentalCancelRefundPermissions = [
            'rental.cancel_refund.view_any',
            'rental.cancel_refund.view',
            'rental.cancel_refund.manage',
        ];

        $rentalPaymentPermissions = [
            'rental.payments.view_any',
            'rental.payments.view',
            'rental.payments.create',
            'rental.payments.refund',
        ];

        $rentalPrintPermissions = [
            'rental.print.contract',
            'rental.print.invoice',
        ];

        $rentalReportPermissions = [
            'rental.reports.view',
        ];

        $rentalAllPermissions = array_merge(
            $rentalContractPermissions,
            $rentalRequestPermissions,
            $rentalCancelRefundPermissions,
            $rentalPaymentPermissions,
            $rentalPrintPermissions,
            $rentalReportPermissions
        );

        $rentalPermissions = [];
        foreach ($rentalAllPermissions as $permName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );
            $rentalPermissions[] = $permission;
            $allPermissions[] = $permName;
        }

        $this->command->info("✓ Rental Contracts permissions created: " . count($rentalPermissions));
        $this->command->newLine();

        // ============================================
        // Assign Permissions to Roles
        // ============================================
        $this->command->info('Step 3: Assigning permissions to roles...');
        $this->command->newLine();

        // Assign to Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($allPermissions);
        $this->command->info('✓ All contracts permissions assigned to Admin role');

        // Assign to super_admin role if exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
            $this->command->info('✓ All contracts permissions assigned to super_admin role');
        } else {
            $this->command->info('ℹ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }

        // ============================================
        // Summary
        // ============================================
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  CONTRACTS PERMISSIONS SUMMARY');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("  Recruitment Contracts: " . count($recruitmentPermissions) . " permissions");
        $this->command->info("  Rental Contracts: " . count($rentalPermissions) . " permissions");
        $this->command->info("  Total Permissions: " . count($allPermissions));
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();
    }
}
