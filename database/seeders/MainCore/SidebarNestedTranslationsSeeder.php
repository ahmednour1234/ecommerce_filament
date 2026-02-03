<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class SidebarNestedTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping sidebar translations.');
            return;
        }

        $translations = [
            'sidebar.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.main_settings' => ['en' => 'Main Settings', 'ar' => 'الإعدادات الرئيسية'],
            'sidebar.main_settings.branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'sidebar.main_settings.currencies' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'sidebar.main_settings.languages' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'sidebar.main_settings.translations' => ['en' => 'Translations', 'ar' => 'الترجمات'],
            'sidebar.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'sidebar.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'sidebar.finance.accounts' => ['en' => 'Accounts', 'ar' => 'الحسابات'],
            'sidebar.finance.journals' => ['en' => 'Journals', 'ar' => 'اليوميات'],
            'sidebar.finance.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'sidebar.employment' => ['en' => 'Employment', 'ar' => 'التوظيف'],
            'sidebar.employment.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.employment.agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'sidebar.employment.professions' => ['en' => 'Professions', 'ar' => 'المهن'],
            'sidebar.rent_section' => ['en' => 'Rental Section', 'ar' => 'قسم التأجير'],
            'sidebar.recruitment_housing' => ['en' => 'Recruitment Housing', 'ar' => 'إيواء الاستقدام'],
            'sidebar.recruitment_housing.contracts' => ['en' => 'Contracts', 'ar' => 'العقود'],
            'sidebar.rent_housing' => ['en' => 'Rental Housing', 'ar' => 'إيواء التأجير'],
            'sidebar.rent_housing.contracts' => ['en' => 'Contracts', 'ar' => 'العقود'],
            'sidebar.available_workers' => ['en' => 'Available Workers', 'ar' => 'العمالة المتاحة'],
            'sidebar.housing_requests' => ['en' => 'Housing Requests', 'ar' => 'طلبات الإيواء'],
            'sidebar.workers_salaries' => ['en' => 'Workers Salaries', 'ar' => 'رواتب العمالة'],
            'sidebar.workers_vacations' => ['en' => 'Workers Vacations', 'ar' => 'إجازات العمالة'],
            'sidebar.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'sidebar.reports.finance' => ['en' => 'Finance Reports', 'ar' => 'تقارير مالية'],
            'sidebar.reports.hr' => ['en' => 'HR Reports', 'ar' => 'تقارير الموارد البشرية'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            $resultEn = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $english->id,
                ],
                [
                    'value' => $values['en'],
                ]
            );

            $resultAr = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );

            if ($resultEn->wasRecentlyCreated || $resultAr->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Sidebar nested translations seeded: {$created} created, {$updated} updated.");
    }
}
