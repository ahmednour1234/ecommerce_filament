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

        // مصروفات (حسب القائمة بالصورة)
        $expenseTypes = [
            ['name' => ['ar' => 'مشتريات أصول'], 'code' => 'ASSET_PURCHASES', 'sort' => 1],
            ['name' => ['ar' => 'مردودات المبيعات'], 'code' => 'SALES_RETURNS', 'sort' => 2],
            ['name' => ['ar' => 'تكاليف الاستقدام'], 'code' => 'RECRUITMENT_COSTS', 'sort' => 3],
            ['name' => ['ar' => 'الضريبة'], 'code' => 'TAX', 'sort' => 4],
            ['name' => ['ar' => 'مصاريف مباشرة للعقود'], 'code' => 'DIRECT_CONTRACT_EXPENSES', 'sort' => 5],
            ['name' => ['ar' => 'مصروف اتصالات'], 'code' => 'COMMUNICATION_EXPENSES', 'sort' => 6],
            ['name' => ['ar' => 'تذاكر سفر'], 'code' => 'TRAVEL_TICKETS', 'sort' => 7],
            ['name' => ['ar' => 'مصروف المراجعات'], 'code' => 'AUDIT_EXPENSES', 'sort' => 8],
            ['name' => ['ar' => 'فحص طبي'], 'code' => 'MEDICAL_EXAM', 'sort' => 9],
            ['name' => ['ar' => 'تأشيرات بديلة'], 'code' => 'ALTERNATIVE_VISAS', 'sort' => 10],
            ['name' => ['ar' => 'ايجار المكتب'], 'code' => 'OFFICE_RENT', 'sort' => 11],
            ['name' => ['ar' => 'ايجار سكن العمل'], 'code' => 'STAFF_HOUSING_RENT', 'sort' => 12],
            ['name' => ['ar' => 'مصروفات الإعاشة'], 'code' => 'LIVING_EXPENSES', 'sort' => 13],
            ['name' => ['ar' => 'مرتبات واجور'], 'code' => 'SALARIES_WAGES', 'sort' => 14],
            ['name' => ['ar' => 'سلف موظفين'], 'code' => 'STAFF_ADVANCES', 'sort' => 15],
            ['name' => ['ar' => 'كهرباء'], 'code' => 'ELECTRICITY', 'sort' => 16],
            ['name' => ['ar' => 'قرطاسية'], 'code' => 'STATIONERY', 'sort' => 17],
            ['name' => ['ar' => 'ضيافة'], 'code' => 'HOSPITALITY', 'sort' => 18],
            ['name' => ['ar' => 'اتصالات ونت'], 'code' => 'INTERNET_PHONE', 'sort' => 19],
            ['name' => ['ar' => 'تامينات اجتماعية'], 'code' => 'SOCIAL_INSURANCE', 'sort' => 20],
            ['name' => ['ar' => 'تسويق ودعاية واعلان'], 'code' => 'MARKETING_ADS', 'sort' => 21],
            ['name' => ['ar' => 'تجديد اقامات'], 'code' => 'IQAMA_RENEWAL', 'sort' => 22],
            ['name' => ['ar' => 'نقل كفالات'], 'code' => 'SPONSORSHIP_TRANSFER', 'sort' => 23],
            ['name' => ['ar' => 'مخالفات وغرامات'], 'code' => 'VIOLATIONS_FINES', 'sort' => 24],
            ['name' => ['ar' => 'عمولات'], 'code' => 'COMMISSIONS', 'sort' => 25],
            ['name' => ['ar' => 'رسوم حكومية'], 'code' => 'GOVERNMENT_FEES', 'sort' => 26],
            ['name' => ['ar' => 'مصروفات الصيانة'], 'code' => 'MAINTENANCE_EXPENSES', 'sort' => 27],
            ['name' => ['ar' => 'مصروفات إدارية'], 'code' => 'ADMIN_EXPENSES', 'sort' => 28],
            ['name' => ['ar' => 'مسحوبات الجارى'], 'code' => 'CURRENT_WITHDRAWALS', 'sort' => 29],
            ['name' => ['ar' => 'وكالة حدود الذكاء'], 'code' => 'HODOOD_ALTHAKA_AGENCY', 'sort' => 30],
            ['name' => ['ar' => 'مصروفات متنوعة'], 'code' => 'MISC_EXPENSES', 'sort' => 31],
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
