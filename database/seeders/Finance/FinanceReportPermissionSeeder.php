<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FinanceReportPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'finance_reports.view',
            'finance_reports.export',
            'finance_reports.print',
        ];

        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $this->command->info('✓ Finance report permissions created: ' . count($perms) . ' permissions');
    }
}
