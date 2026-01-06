<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HrTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR module translations (Arabic and English).
     */
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping HR translations.');
            return;
        }

        $this->command->info('Creating HR module translations...');

        $translations = [
            // Navigation Group
            'navigation.groups.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'sidebar.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],

            // Navigation Items
            'navigation.hr_departments' => ['en' => 'Departments', 'ar' => 'الإدارات'],
            'navigation.hr_positions' => ['en' => 'Positions', 'ar' => 'المسميات الوظيفية'],
            'navigation.hr_blood_types' => ['en' => 'Blood Types', 'ar' => 'فصائل الدم'],
            'navigation.hr_identity_types' => ['en' => 'Identity Types', 'ar' => 'نوع الهوية'],
            'navigation.hr_banks' => ['en' => 'Banks', 'ar' => 'البنوك'],

            // Common Fields
            'fields.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'fields.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'fields.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Actions
            'actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'actions.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],

            // Messages
            'messages.saved_successfully' => ['en' => 'Saved successfully', 'ar' => 'تم الحفظ بنجاح'],

            // Departments Forms
            'forms.hr_departments.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_departments.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Departments Tables
            'tables.hr_departments.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_departments.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_departments.positions_count' => ['en' => 'Positions', 'ar' => 'الوظائف'],
            'tables.hr_departments.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_departments.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_departments.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Positions Forms
            'forms.hr_positions.title.label' => ['en' => 'Title', 'ar' => 'العنوان'],
            'forms.hr_positions.department_id.label' => ['en' => 'Department', 'ar' => 'الإدارة'],
            'forms.hr_positions.description.label' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.hr_positions.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Positions Tables
            'tables.hr_positions.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'tables.hr_positions.department' => ['en' => 'Department', 'ar' => 'الإدارة'],
            'tables.hr_positions.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'tables.hr_positions.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_positions.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_positions.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_positions.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_positions.filters.department' => ['en' => 'Department', 'ar' => 'الإدارة'],

            // Blood Types Forms
            'forms.hr_blood_types.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_blood_types.code.label' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.hr_blood_types.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Blood Types Tables
            'tables.hr_blood_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_blood_types.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.hr_blood_types.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_blood_types.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_blood_types.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_blood_types.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Identity Types Forms
            'forms.hr_identity_types.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_identity_types.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Identity Types Tables
            'tables.hr_identity_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_identity_types.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_identity_types.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_identity_types.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_identity_types.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Banks Forms
            'forms.hr_banks.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_banks.iban_prefix.label' => ['en' => 'IBAN Prefix', 'ar' => 'بادئة IBAN'],
            'forms.hr_banks.iban_prefix.helper' => ['en' => 'Optional IBAN prefix code', 'ar' => 'رمز البادئة IBAN (اختياري)'],
            'forms.hr_banks.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Banks Tables
            'tables.hr_banks.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_banks.iban_prefix' => ['en' => 'IBAN Prefix', 'ar' => 'بادئة IBAN'],
            'tables.hr_banks.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_banks.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_banks.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_banks.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],
        ];

        $created = 0;
        foreach ($translations as $key => $values) {
            // English translation
            if (isset($values['en'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $english->id,
                    ],
                    ['value' => $values['en']]
                );
                $created++;
            }

            // Arabic translation
            if (isset($values['ar'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $arabic->id,
                    ],
                    ['value' => $values['ar']]
                );
                $created++;
            }
        }

        $this->command->info("✓ HR translations created: {$created} entries");
    }
}

