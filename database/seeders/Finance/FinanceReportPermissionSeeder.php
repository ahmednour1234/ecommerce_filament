<?php

namespace Database\Seeders\Finance;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FinanceReportPermissionSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'finance.reports.view',
            'finance.reports.export',
            'finance.reports.print',
            'finance.reports.pdf',
        ];

        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
    }
}
