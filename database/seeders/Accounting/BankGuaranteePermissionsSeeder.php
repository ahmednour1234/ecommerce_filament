<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BankGuaranteePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Bank Guarantee Permissions
        $bankGuaranteePermissions = [
            'bank_guarantees.view_any',
            'bank_guarantees.view',
            'bank_guarantees.create',
            'bank_guarantees.update',
            'bank_guarantees.delete',
            'bank_guarantees.renew',
        ];

        // Create permissions
        foreach ($bankGuaranteePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles (if roles exist)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($bankGuaranteePermissions);
        }

        $accountantRole = Role::where('name', 'accountant')->first();
        if ($accountantRole) {
            $accountantRole->givePermissionTo($bankGuaranteePermissions);
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'bank_guarantees.view_any',
                'bank_guarantees.view',
                'bank_guarantees.renew',
            ]);
        }
    }
}

