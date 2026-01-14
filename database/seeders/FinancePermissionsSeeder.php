<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FinancePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'branch_tx.view_any',
            'branch_tx.view',
            'branch_tx.view_all_branches',
            'branch_tx.create',
            'branch_tx.update',
            'branch_tx.delete',
            'branch_tx.approve',
            'branch_tx.reject',
            'branch_tx.export',
            'branch_tx.print',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
    }
}
