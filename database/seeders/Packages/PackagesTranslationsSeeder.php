<?php

namespace Database\Seeders\Packages;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class PackagesTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping packages translations.');
            return;
        }

        $translations = [
            'navigation.offers' => ['en' => 'Packages', 'ar' => 'باقات العروض'],
            'navigation.recruitment' => ['en' => 'Recruitment Packages', 'ar' => 'باقات الاستقدام'],
            'navigation.rental' => ['en' => 'Rental Packages', 'ar' => 'باقات التأجير'],
            'navigation.service_transfer' => ['en' => 'Service Transfer Packages', 'ar' => 'باقات نقل الخدمات'],

            'fields.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'fields.duration_type' => ['en' => 'Duration Type', 'ar' => 'نوع المدة'],
            'fields.duration' => ['en' => 'Duration', 'ar' => 'المدة'],
            'fields.base_price' => ['en' => 'Base Price', 'ar' => 'السعر الأساسي'],
            'fields.external_costs' => ['en' => 'External Costs', 'ar' => 'التكاليف الخارجية'],
            'fields.worker_salary' => ['en' => 'Worker Salary', 'ar' => 'راتب العامل'],
            'fields.gov_fees' => ['en' => 'Government Fees', 'ar' => 'رسوم الحكومة'],
            'fields.tax_percent' => ['en' => 'Tax Percent', 'ar' => 'نسبة الضريبة'],
            'fields.tax_value' => ['en' => 'Tax Value', 'ar' => 'قيمة الضريبة'],
            'fields.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],

            'fields.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'fields.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'fields.direct_cost' => ['en' => 'Direct Cost', 'ar' => 'التكلفة المباشرة'],
            'fields.gov_cost' => ['en' => 'Government Cost', 'ar' => 'تكلفة الحكومة'],
            'fields.external_cost' => ['en' => 'External Cost', 'ar' => 'التكلفة الخارجية'],
            'fields.total_with_tax' => ['en' => 'Total with Tax', 'ar' => 'الإجمالي مع الضريبة'],

            'buttons.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'buttons.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'buttons.export_pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],

            'pdf.title' => ['en' => 'Package Details', 'ar' => 'تفاصيل الباقة'],

            'types.recruitment' => ['en' => 'Recruitment', 'ar' => 'استقدام'],
            'types.rental' => ['en' => 'Rental', 'ar' => 'تأجير'],
            'types.service_transfer' => ['en' => 'Service Transfer', 'ar' => 'نقل الخدمات'],

            'status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'status.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],

            'duration_types.day' => ['en' => 'Day', 'ar' => 'يوم'],
            'duration_types.month' => ['en' => 'Month', 'ar' => 'شهر'],
            'duration_types.year' => ['en' => 'Year', 'ar' => 'سنة'],
        ];

        $group = 'packages';
        $created = 0;

        foreach ($translations as $key => $values) {
            foreach (['en' => $english, 'ar' => $arabic] as $langCode => $language) {
                if (!isset($values[$langCode])) {
                    continue;
                }

                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => $group,
                        'language_id' => $language->id,
                    ],
                    [
                        'value' => $values[$langCode],
                    ]
                );
                $created++;
            }
        }

        $this->command->info("✓ Packages translations created/updated: {$created}");
    }
}
