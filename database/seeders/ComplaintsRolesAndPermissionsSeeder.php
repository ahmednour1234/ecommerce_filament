<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Creates / updates all permissions and roles for the Complaints module,
 * including the two-tab messaging system (قسم الشكاوي / قسم التنسيق).
 *
 * Roles:
 *  - مدير الشكاوى          → full CRUD + complaints-tab messages   (TYPE_COMPLAINTS_MANAGER)
 *  - مشرف قسم الشكاوي      → full CRUD + complaints-tab messages   (TYPE_COMPLAINTS_MANAGER, lighter)
 *  - منسق الشكاوي           → view only + coordination-tab messages (TYPE_COORDINATOR)
 */
class ComplaintsRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('── Complaints: permissions & roles seeder ──');

        // ──────────────────────────────────────────────────────────
        // 1. PERMISSIONS
        // ──────────────────────────────────────────────────────────
        $crudActions = ['view_any', 'view', 'create', 'update', 'delete'];
        $allPerms = [];

        foreach ($crudActions as $action) {
            $allPerms[] = Permission::firstOrCreate(
                ['name' => "complaints.{$action}", 'guard_name' => 'web']
            );
        }

        // Message permissions (department-scoped)
        $allPerms[] = Permission::firstOrCreate(
            ['name' => 'complaints.messages.send_complaints',   'guard_name' => 'web']
        );
        $allPerms[] = Permission::firstOrCreate(
            ['name' => 'complaints.messages.send_coordination', 'guard_name' => 'web']
        );

        $this->command->info('✓ ' . count($allPerms) . ' permissions ensured');

        // ──────────────────────────────────────────────────────────
        // 2. ROLES
        // ──────────────────────────────────────────────────────────

        // ----- Role A: مدير الشكاوى (TYPE_COMPLAINTS_MANAGER) -----
        //   Full CRUD + can post to "قسم الشكاوي" message tab
        $complaintsManagerRole = Role::firstOrCreate(
            ['name' => 'مدير الشكاوى', 'guard_name' => 'web']
        );
        $complaintsManagerRole->syncPermissions(
            Permission::whereIn('name', [
                'complaints.view_any',
                'complaints.view',
                'complaints.create',
                'complaints.update',
                'complaints.delete',
                'complaints.messages.send_complaints',
                'complaints.messages.send_coordination',
            ])->get()
        );
        $this->command->info('✓ Role "مدير الشكاوى" synced (TYPE_COMPLAINTS_MANAGER)');

        // ----- Role B: مشرف قسم الشكاوي (TYPE_COMPLAINTS_MANAGER, lighter) -----
        $complaintsOfficerRole = Role::firstOrCreate(
            ['name' => 'مشرف قسم الشكاوي', 'guard_name' => 'web']
        );
        $complaintsOfficerRole->syncPermissions(
            Permission::whereIn('name', [
                'complaints.view_any',
                'complaints.view',
                'complaints.create',
                'complaints.update',
                'complaints.delete',
                'complaints.messages.send_complaints',
                'complaints.messages.send_coordination',
            ])->get()
        );
        $this->command->info('✓ Role "مشرف قسم الشكاوي" synced (TYPE_COMPLAINTS_MANAGER)');

        // ----- Role C: منسق الشكاوي (TYPE_COORDINATOR) -----
        //   Read-only on complaints + can only post to "قسم التنسيق" message tab
        $coordinatorRole = Role::firstOrCreate(
            ['name' => 'منسق الشكاوي', 'guard_name' => 'web']
        );
        $coordinatorRole->syncPermissions(
            Permission::whereIn('name', [
                'complaints.view_any',
                'complaints.view',
                'complaints.messages.send_coordination',
            ])->get()
        );
        $this->command->info('✓ Role "منسق الشكاوي" synced (TYPE_COORDINATOR)');

        // ──────────────────────────────────────────────────────────
        // 3. Assign ALL complaint permissions to Admin & Super Admin
        // ──────────────────────────────────────────────────────────
        foreach (['Admin', 'super_admin', 'Super Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($allPerms);
                $this->command->info("✓ All complaints permissions granted to \"{$roleName}\"");
            }
        }

        // ──────────────────────────────────────────────────────────
        // 4. Auto-assign roles to existing users by type
        // ──────────────────────────────────────────────────────────
        $complaintsManagerUsers = User::where('type', User::TYPE_COMPLAINTS_MANAGER)->get();
        foreach ($complaintsManagerUsers as $user) {
            if (!$user->hasRole('مدير الشكاوى')) {
                $user->assignRole('مدير الشكاوى');
            }
        }
        $this->command->info("✓ Role \"مدير الشكاوى\" assigned to {$complaintsManagerUsers->count()} TYPE_COMPLAINTS_MANAGER users");

        $coordinatorUsers = User::where('type', User::TYPE_COORDINATOR)->get();
        foreach ($coordinatorUsers as $user) {
            if (!$user->hasRole('منسق الشكاوي')) {
                $user->assignRole('منسق الشكاوي');
            }
        }
        $this->command->info("✓ Role \"منسق الشكاوي\" assigned to {$coordinatorUsers->count()} TYPE_COORDINATOR users");

        $this->command->info('── Complaints seeder complete ──');
    }
}
