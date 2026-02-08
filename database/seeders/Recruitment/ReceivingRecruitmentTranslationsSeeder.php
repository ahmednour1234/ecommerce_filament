<?php

namespace Database\Seeders\Recruitment;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class ReceivingRecruitmentTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping receiving recruitment translations.');
            return;
        }

        $translations = [
            'recruitment.receiving_labor.title' => [
                'en' => 'Receiving Labor',
                'ar' => 'استلام العمالة',
            ],
            'recruitment.receiving_labor.breadcrumb_home' => [
                'en' => 'Home',
                'ar' => 'الرئيسية',
            ],
            'recruitment.receiving_labor.breadcrumb_recruitment' => [
                'en' => 'Recruitment',
                'ar' => 'الاستقدام',
            ],
            'recruitment.receiving_labor.table.id' => [
                'en' => 'ID',
                'ar' => 'رقم',
            ],
            'recruitment.receiving_labor.table.client' => [
                'en' => 'Client',
                'ar' => 'العميل',
            ],
            'recruitment.receiving_labor.table.worker' => [
                'en' => 'Worker Name',
                'ar' => 'اسم العامل',
            ],
            'recruitment.receiving_labor.table.passport' => [
                'en' => 'Passport Number',
                'ar' => 'رقم الجواز',
            ],
            'recruitment.receiving_labor.table.arrival_date' => [
                'en' => 'Arrival Date',
                'ar' => 'تاريخ الوصول',
            ],
            'recruitment.receiving_labor.table.trial_end_date' => [
                'en' => 'Trial End Date',
                'ar' => 'تاريخ نهاية فترة التجربة',
            ],
            'recruitment.receiving_labor.table.contract_end_date' => [
                'en' => 'Contract End Date',
                'ar' => 'تاريخ نهاية العقد',
            ],
            'recruitment.receiving_labor.table.status' => [
                'en' => 'Status',
                'ar' => 'حالة الطلب',
            ],
            'recruitment.receiving_labor.table.employee' => [
                'en' => 'Employee',
                'ar' => 'الموظف',
            ],
            'recruitment.receiving_labor.status.received' => [
                'en' => 'Received',
                'ar' => 'تم الاستلام',
            ],
            'recruitment.receiving_labor.status.pending' => [
                'en' => 'Pending',
                'ar' => 'قيد الانتظار',
            ],
            'recruitment.receiving_labor.status.canceled' => [
                'en' => 'Canceled',
                'ar' => 'ملغي',
            ],
            'actions.view' => [
                'en' => 'View',
                'ar' => 'عرض',
            ],
            'actions.export_excel' => [
                'en' => 'Export to Excel',
                'ar' => 'تصدير Excel',
            ],
            'actions.print' => [
                'en' => 'Print',
                'ar' => 'طباعة',
            ],
            'sidebar.recruitment.receiving_labor' => [
                'en' => 'Receiving Labor',
                'ar' => 'استلام العمالة',
            ],
            'sidebar.recruitment_contracts' => [
                'en' => 'Recruitment Contracts',
                'ar' => 'عقود الاستقدام',
            ],
            'sidebar.recruitment' => [
                'en' => 'Recruitment',
                'ar' => 'التوظيف',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            // English translation
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

            // Arabic translation
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

        $this->command->info("Receiving recruitment translations seeded: {$created} created, {$updated} updated.");
    }
}
