<?php

namespace Database\Seeders\Finance;

use App\Models\Finance\FinanceType;
use Illuminate\Database\Seeder;

class FinanceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $incomeTypes = [
            ['name' => ['ar' => 'إيرادات الاستقدام'], 'code' => 'RECRUITMENT', 'sort' => 1],
            ['name' => ['ar' => 'إيرادات التفاوضات الرجالية'], 'code' => 'MALE_NEGOTIATIONS', 'sort' => 2],
            ['name' => ['ar' => 'إيرادات الـ PVT'], 'code' => 'PVT', 'sort' => 3],
            ['name' => ['ar' => 'إيرادات التنازلات'], 'code' => 'WAIVERS', 'sort' => 4],
            ['name' => ['ar' => 'إيرادات التسويات'], 'code' => 'SETTLEMENTS', 'sort' => 5],
            ['name' => ['ar' => 'إيرادات الإيجارات'], 'code' => 'RENTALS', 'sort' => 6],
            ['name' => ['ar' => 'إيرادات أخرى'], 'code' => 'OTHER_INCOME', 'sort' => 7],
        ];

        $expenseTypes = [
            ['name' => ['ar' => 'رواتب وأجور'], 'code' => 'SALARIES', 'sort' => 1],
            ['name' => ['ar' => 'إيجارات'], 'code' => 'RENT', 'sort' => 2],
            ['name' => ['ar' => 'تسويق وإعلانات'], 'code' => 'MARKETING', 'sort' => 3],
            ['name' => ['ar' => 'مصاريف تشغيل'], 'code' => 'OPERATING', 'sort' => 4],
            ['name' => ['ar' => 'مصاريف حكومية ورسوم'], 'code' => 'GOVERNMENT', 'sort' => 5],
            ['name' => ['ar' => 'نقل ومواصلات'], 'code' => 'TRANSPORT', 'sort' => 6],
            ['name' => ['ar' => 'صيانة'], 'code' => 'MAINTENANCE', 'sort' => 7],
            ['name' => ['ar' => 'عمولات'], 'code' => 'COMMISSIONS', 'sort' => 8],
            ['name' => ['ar' => 'مصاريف بنكية'], 'code' => 'BANK', 'sort' => 9],
            ['name' => ['ar' => 'مصاريف أخرى'], 'code' => 'OTHER_EXPENSE', 'sort' => 10],
        ];

        foreach ($incomeTypes as $type) {
            FinanceType::updateOrCreate(
                [
                    'kind' => 'income',
                    'code' => $type['code'],
                ],
                [
                    'name' => $type['name'],
                    'sort' => $type['sort'],
                    'is_active' => true,
                ]
            );
        }

        foreach ($expenseTypes as $type) {
            FinanceType::updateOrCreate(
                [
                    'kind' => 'expense',
                    'code' => $type['code'],
                ],
                [
                    'name' => $type['name'],
                    'sort' => $type['sort'],
                    'is_active' => true,
                ]
            );
        }
    }
}
