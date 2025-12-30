<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates/updates the super_admin role and assigns
     * ALL permissions in the system to it, ensuring full access.
     * 
     * Can be run standalone: php artisan db:seed --class=SuperAdminSeeder
     */
    public function run(): void
    {
        $this->command->info('Creating/updating super_admin role...');

        // First, ensure all permissions are created by running other seeders
        // Only call if they haven't been run (to avoid duplicate calls in DatabaseSeeder)
        try {
            if (!Permission::where('guard_name', 'web')->exists()) {
                $this->command->info('Creating base permissions...');
                $this->call([
                    RolePermissionSeeder::class,
                ]);
            }

            // Always run accounting permissions seeder as it may have new permissions
            $this->command->info('Creating accounting permissions...');
            $this->call([
                \Database\Seeders\Accounting\AccountingPermissionsSeeder::class,
            ]);
        } catch (\Exception $e) {
            $this->command->warn("Warning: Could not run permission seeders: {$e->getMessage()}");
            $this->command->info('Continuing with existing permissions...');
        }

        // Get ALL permissions from the database
        $allPermissions = Permission::where('guard_name', 'web')->get();

        if ($allPermissions->isEmpty()) {
            $this->command->error('No permissions found in database!');
            $this->command->info('Please run: php artisan db:seed --class=RolePermissionSeeder');
            return;
        }

        // Create or get super_admin role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // Get current permission count
        $currentCount = $superAdmin->permissions()->count();

        // Sync ALL permissions to super_admin role
        $superAdmin->syncPermissions($allPermissions);

        $newCount = $allPermissions->count();
        $this->command->info("✓ Super Admin role created/updated");
        $this->command->info("  Previous permissions: {$currentCount}");
        $this->command->info("  Current permissions: {$newCount}");

        // Optionally assign super_admin role to specific users
        $this->assignSuperAdminToUsers();

        // Display summary
        $this->displaySummary($superAdmin);
    }

    /**
     * Assign super_admin role to admin users
     */
    protected function assignSuperAdminToUsers(): void
    {
        $assigned = 0;

        // Assign to admin@example.com if exists
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            if (!$admin->hasRole('super_admin')) {
                $admin->assignRole('super_admin');
                $this->command->info("✓ Assigned super_admin role to: {$admin->email}");
                $assigned++;
            } else {
                $this->command->info("✓ User {$admin->email} already has super_admin role");
            }
        }

        // If no admin user exists, assign to first user
        if (!$admin) {
            $firstUser = User::first();
            if ($firstUser) {
                if (!$firstUser->hasRole('super_admin')) {
                    $firstUser->assignRole('super_admin');
                    $this->command->info("✓ Assigned super_admin role to first user: {$firstUser->email}");
                    $assigned++;
                } else {
                    $this->command->info("✓ User {$firstUser->email} already has super_admin role");
                }
            } else {
                $this->command->warn("⚠ No users found. Create a user first to assign super_admin role.");
            }
        }

        if ($assigned === 0 && User::exists()) {
            $this->command->info("ℹ All existing users already have super_admin role or no users to assign");
        }
    }

    /**
     * Display summary of super_admin role
     */
    protected function displaySummary(Role $role): void
    {
        $permissionCount = $role->permissions()->count();
        $userCount = $role->users()->count();

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  SUPER ADMIN ROLE SUMMARY');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("  Role Name: {$role->name}");
        $this->command->info("  Total Permissions: {$permissionCount}");
        $this->command->info("  Users with this role: {$userCount}");
        
        if ($userCount > 0) {
            $this->command->info('  Users:');
            foreach ($role->users as $user) {
                $this->command->info("    - {$user->name} ({$user->email})");
            }
        }
        
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();
    }
}

