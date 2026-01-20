<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FinancePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all Finance module permissions.
     */
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating Finance module permissions...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        // ============================================
        // Finance Types Permissions
        // ============================================
        $this->command->info('Step 1: Creating Finance Types permissions...');
        $typePermissions = [
            'finance.view_types',
            'finance.manage_types',
        ];

        // ============================================
        // Branch Transactions Permissions
        // ============================================
        $this->command->info('Step 2: Creating Branch Transactions permissions...');
        $transactionPermissions = [
            'finance.view_transactions',
            'finance.create_transactions',
            'finance.update_transactions',
            'finance.delete_transactions',
            'finance.approve_transactions',
            'finance.reject_transactions',
            'finance.transactions.import',
        ];

        // ============================================
        // Reports Permissions
        // ============================================
        $this->command->info('Step 3: Creating Reports permissions...');
        $reportPermissions = [
            'finance.view_reports',
        ];

        // ============================================
        // Combine all permissions
        // ============================================
        $allPermissions = array_merge(
            $typePermissions,
            $transactionPermissions,
            $reportPermissions
        );

        $createdPermissions = [];

        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            $createdPermissions[] = $permission;
        }

        $this->command->info("✓ Finance permissions created: " . count($createdPermissions));
        $this->command->newLine();

        // ============================================
        // Assign permissions to roles
        // ============================================
        $this->command->info('Step 4: Assigning permissions to roles...');

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
            $this->command->info('✓ All permissions assigned to super_admin role');
        } else {
            $this->command->warn('⚠ super_admin role not found. Permissions will be assigned by SuperAdminSeeder.');
        }

        $financeRole = Role::where('name', 'finance')->first();
        if ($financeRole) {
            $financeRole->givePermissionTo($allPermissions);
            $this->command->info('✓ All permissions assigned to finance role');
        } else {
            $this->command->info('ℹ finance role not found. Create it manually if needed.');
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✓ Finance permissions seeder completed successfully!');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
