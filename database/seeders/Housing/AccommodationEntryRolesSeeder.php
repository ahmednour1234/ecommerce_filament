<?php

namespace Database\Seeders\Housing;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccommodationEntryRolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Accommodation Entry permissions and roles...');

        // Ensure all required permissions exist (add the missing "view" action)
        $allPermissions = [
            'housing.accommodation_entries.view_any',
            'housing.accommodation_entries.view',
            'housing.accommodation_entries.create',
            'housing.accommodation_entries.update',
            'housing.accommodation_entries.delete',
        ];

        foreach ($allPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        $this->command->info('✓ Accommodation entry permissions ensured');

        // ---------------------------------------------------------------
        // Role 1: منسق إيواء (Housing Coordinator) – read-only access
        //   Matches User::TYPE_COORDINATOR — sees the list/view but cannot edit
        // ---------------------------------------------------------------
        $coordinatorRole = Role::firstOrCreate([
            'name'       => 'منسق إيواء',
            'guard_name' => 'web',
        ]);

        $coordinatorPermissions = Permission::whereIn('name', [
            'housing.accommodation_entries.view_any',
            'housing.accommodation_entries.view',
        ])->get();

        $coordinatorRole->syncPermissions($coordinatorPermissions);

        $this->command->info('✓ Role "منسق إيواء" created with ' . $coordinatorPermissions->count() . ' permissions (read-only)');

        // ---------------------------------------------------------------
        // Role 2: مدير شكاوي الإيواء (Housing Complaints Manager) – full CRUD
        //   Matches User::TYPE_COMPLAINTS_MANAGER — can create, edit, delete
        // ---------------------------------------------------------------
        $complaintsManagerRole = Role::firstOrCreate([
            'name'       => 'مدير شكاوي الإيواء',
            'guard_name' => 'web',
        ]);

        $complaintsPermissions = Permission::whereIn('name', [
            'housing.accommodation_entries.view_any',
            'housing.accommodation_entries.view',
            'housing.accommodation_entries.create',
            'housing.accommodation_entries.update',
            'housing.accommodation_entries.delete',
        ])->get();

        $complaintsManagerRole->syncPermissions($complaintsPermissions);

        $this->command->info('✓ Role "مدير شكاوي الإيواء" created with ' . $complaintsPermissions->count() . ' permissions (full CRUD)');

        // ---------------------------------------------------------------
        // Grant the new "view" permission to Admin and Super Admin roles
        // ---------------------------------------------------------------
        $viewPermission = Permission::where('name', 'housing.accommodation_entries.view')->first();

        if ($viewPermission) {
            $adminRole = Role::where('name', 'Admin')->first();
            if ($adminRole) {
                $adminRole->givePermissionTo($viewPermission);
                $this->command->info('✓ "housing.accommodation_entries.view" assigned to Admin role');
            }

            $superAdminRole = Role::where('name', 'super_admin')
                ->orWhere('name', 'Super Admin')
                ->first();
            if ($superAdminRole) {
                $superAdminRole->givePermissionTo($viewPermission);
                $this->command->info('✓ "housing.accommodation_entries.view" assigned to Super Admin role');
            }

            // Also assign the full set to Housing Manager role if it exists
            $housingManagerRole = Role::where('name', 'مدير قسم الإيواء')->first();
            if ($housingManagerRole) {
                $housingManagerRole->givePermissionTo($viewPermission);
                $this->command->info('✓ "housing.accommodation_entries.view" assigned to مدير قسم الإيواء role');
            }
        }

        $this->command->info('✓ Accommodation Entry roles seeder completed');
    }
}
