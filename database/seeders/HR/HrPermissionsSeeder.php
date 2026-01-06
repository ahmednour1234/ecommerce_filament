<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class HrPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR module permissions.
     */
    public function run(): void
    {
        $this->command->info('Creating HR module permissions...');

        // HR Resources
        $hrResources = [
            'hr_departments',
            'hr_positions',
            'hr_blood_types',
            'hr_identity_types',
            'hr_banks',
            'hr_employees',
        ];

        $permissions = [];

        // Generate permissions for all HR resources
        foreach ($hrResources as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete'] as $action) {
                $permName = "{$resource}.{$action}";
                
                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web']
                );
                
                $permissions[] = $permission;
            }
        }

        $this->command->info('✓ HR permissions created: ' . count($permissions));
        
        // Note: Permission assignment to super_admin role is handled by SuperAdminSeeder
        // which automatically syncs all permissions to super_admin role
    }
}

