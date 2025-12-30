<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Period;
use Illuminate\Database\Seeder;

class FiscalYearSeeder extends Seeder
{
    public function run(): void
    {
        // Create current fiscal year
        $currentYear = date('Y');
        $fiscalYear = FiscalYear::updateOrCreate(
            ['name' => "FY {$currentYear}"],
            [
                'start_date' => "{$currentYear}-01-01",
                'end_date' => "{$currentYear}-12-31",
                'is_active' => true,
                'is_closed' => false,
            ]
        );

        // Create periods for the fiscal year
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        foreach ($months as $monthNum => $monthName) {
            Period::updateOrCreate(
                [
                    'fiscal_year_id' => $fiscalYear->id,
                    'period_number' => $monthNum,
                ],
                [
                    'name' => "{$monthName} {$currentYear}",
                    'start_date' => "{$currentYear}-" . str_pad($monthNum, 2, '0', STR_PAD_LEFT) . "-01",
                    'end_date' => date('Y-m-t', strtotime("{$currentYear}-" . str_pad($monthNum, 2, '0', STR_PAD_LEFT) . "-01")),
                    'is_closed' => false,
                ]
            );
        }
    }
}

