<?php

namespace Database\Seeders\Recruitment;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class RecruitmentTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Recruitment translations.');
            return;
        }

        $this->command->info('Creating Recruitment module translations...');

        $translations = [
            'navigation.groups.recruitment' => ['en' => 'Recruitment', 'ar' => 'التوظيف'],
            'sidebar.recruitment' => ['en' => 'Recruitment', 'ar' => 'التوظيف'],
            'navigation.recruitment_agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'navigation.recruitment_agent_prices' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            'navigation.recruitment_nationalities' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'navigation.recruitment_professions' => ['en' => 'Professions', 'ar' => 'المهن'],
            'navigation.recruitment_laborers' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'navigation.recruitment_laborers_used' => ['en' => 'Used Laborers', 'ar' => 'العمال المستخدمون'],
            'sidebar.recruitment.agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'sidebar.recruitment.agent_prices' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            'sidebar.recruitment.nationalities' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'sidebar.recruitment.professions' => ['en' => 'Professions', 'ar' => 'المهن'],
            'sidebar.recruitment.laborers' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'sidebar.recruitment.laborers_used' => ['en' => 'Used Laborers', 'ar' => 'العمال المستخدمون'],
            'recruitment.agents.title' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'recruitment.agent_prices.title' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            'recruitment.nationalities.title' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'recruitment.professions.title' => ['en' => 'Professions', 'ar' => 'المهن'],
            'recruitment.laborers.title' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'recruitment.laborers_used.title' => ['en' => 'Used Laborers', 'ar' => 'العمال المستخدمون'],

            'recruitment.fields.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'recruitment.fields.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'recruitment.fields.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'recruitment.fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'recruitment.fields.phone_1' => ['en' => 'Phone 1', 'ar' => 'الهاتف 1'],
            'recruitment.fields.phone_2' => ['en' => 'Phone 2', 'ar' => 'الهاتف 2'],
            'recruitment.fields.mobile' => ['en' => 'Mobile', 'ar' => 'الجوال'],
            'recruitment.fields.fax' => ['en' => 'Fax', 'ar' => 'الفاكس'],
            'recruitment.fields.license_number' => ['en' => 'License Number', 'ar' => 'رقم الترخيص'],
            'recruitment.fields.address_ar' => ['en' => 'Address (Arabic)', 'ar' => 'العنوان (عربي)'],
            'recruitment.fields.address_en' => ['en' => 'Address (English)', 'ar' => 'العنوان (إنجليزي)'],
            'recruitment.fields.city_ar' => ['en' => 'City (Arabic)', 'ar' => 'المدينة (عربي)'],
            'recruitment.fields.city_en' => ['en' => 'City (English)', 'ar' => 'المدينة (إنجليزي)'],
            'recruitment.fields.passport_number' => ['en' => 'Passport Number', 'ar' => 'رقم الجواز'],
            'recruitment.fields.passport_issue_date' => ['en' => 'Passport Issue Date', 'ar' => 'تاريخ إصدار الجواز'],
            'recruitment.fields.passport_expiry_date' => ['en' => 'Passport Expiry Date', 'ar' => 'تاريخ انتهاء الجواز'],
            'recruitment.fields.passport_issue_place' => ['en' => 'Passport Issue Place', 'ar' => 'مكان إصدار الجواز'],
            'recruitment.fields.birth_date' => ['en' => 'Birth Date', 'ar' => 'تاريخ الميلاد'],
            'recruitment.fields.gender' => ['en' => 'Gender', 'ar' => 'الجنس'],
            'recruitment.fields.gender_male' => ['en' => 'Male', 'ar' => 'ذكر'],
            'recruitment.fields.gender_female' => ['en' => 'Female', 'ar' => 'أنثى'],
            'recruitment.fields.experience_level' => ['en' => 'Experience Level', 'ar' => 'مستوى الخبرة'],
            'recruitment.fields.social_status' => ['en' => 'Social Status', 'ar' => 'الحالة الاجتماعية'],
            'recruitment.fields.address' => ['en' => 'Address', 'ar' => 'العنوان'],
            'recruitment.fields.relative_name' => ['en' => 'Relative Name', 'ar' => 'اسم القريب'],
            'recruitment.fields.nationality' => ['en' => 'Nationality', 'ar' => 'الجنسية'],
            'recruitment.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'recruitment.fields.agent' => ['en' => 'Agent', 'ar' => 'الوكيل'],
            'recruitment.fields.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'recruitment.fields.is_available' => ['en' => 'Is Available', 'ar' => 'متاح'],
            'recruitment.fields.show_on_website' => ['en' => 'Show on Website', 'ar' => 'عرض على الموقع'],
            'recruitment.fields.height' => ['en' => 'Height', 'ar' => 'الطول'],
            'recruitment.fields.weight' => ['en' => 'Weight', 'ar' => 'الوزن'],
            'recruitment.fields.speaks_arabic' => ['en' => 'Speaks Arabic', 'ar' => 'يتحدث العربية'],
            'recruitment.fields.speaks_english' => ['en' => 'Speaks English', 'ar' => 'يتحدث الإنجليزية'],
            'recruitment.fields.personal_image' => ['en' => 'Personal Image', 'ar' => 'الصورة الشخصية'],
            'recruitment.fields.cv_file' => ['en' => 'CV File', 'ar' => 'ملف السيرة الذاتية'],
            'recruitment.fields.intro_video' => ['en' => 'Intro Video', 'ar' => 'فيديو التعريف'],
            'recruitment.fields.monthly_salary' => ['en' => 'Monthly Salary', 'ar' => 'الراتب الشهري'],
            'recruitment.fields.used_at' => ['en' => 'Used At', 'ar' => 'تاريخ الاستخدام'],
            'recruitment.fields.laborer' => ['en' => 'Laborer', 'ar' => 'العامل'],
            'recruitment.fields.bank_sender' => ['en' => 'Bank Sender', 'ar' => 'البنك المرسل'],
            'recruitment.fields.account_number' => ['en' => 'Account Number', 'ar' => 'رقم الحساب'],
            'recruitment.fields.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'recruitment.fields.username' => ['en' => 'Username', 'ar' => 'اسم المستخدم'],
            'recruitment.fields.password' => ['en' => 'Password', 'ar' => 'كلمة المرور'],
            'recruitment.fields.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'recruitment.fields.responsible_name' => ['en' => 'Responsible Name', 'ar' => 'اسم المسؤول'],
            'recruitment.fields.active' => ['en' => 'Active', 'ar' => 'نشط'],

            'recruitment.prices.fields.agent' => ['en' => 'Agent', 'ar' => 'الوكيل'],
            'recruitment.prices.fields.nationality' => ['en' => 'Nationality', 'ar' => 'الجنسية'],
            'recruitment.prices.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'recruitment.prices.fields.experience_level' => ['en' => 'Experience Level', 'ar' => 'مستوى الخبرة'],
            'recruitment.prices.fields.cost_amount' => ['en' => 'Cost Amount', 'ar' => 'المبلغ'],
            'recruitment.prices.fields.currency' => ['en' => 'Currency', 'ar' => 'العملة'],

            'recruitment.sections.basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'recruitment.sections.contact' => ['en' => 'Contact Information', 'ar' => 'معلومات الاتصال'],
            'recruitment.sections.location' => ['en' => 'Location', 'ar' => 'الموقع'],
            'recruitment.sections.identity' => ['en' => 'Identity & Banking', 'ar' => 'الهوية والمصرفية'],
            'recruitment.sections.login' => ['en' => 'Login Credentials', 'ar' => 'بيانات تسجيل الدخول'],
            'recruitment.sections.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'recruitment.sections.classification' => ['en' => 'Classification', 'ar' => 'التصنيف'],
            'recruitment.sections.agent_country' => ['en' => 'Agent & Country', 'ar' => 'الوكيل والدولة'],
            'recruitment.sections.physical' => ['en' => 'Physical Attributes', 'ar' => 'المواصفات البدنية'],
            'recruitment.sections.files' => ['en' => 'Files', 'ar' => 'الملفات'],
            'recruitment.sections.salary' => ['en' => 'Salary', 'ar' => 'الراتب'],
            'recruitment.sections.flags' => ['en' => 'Flags', 'ar' => 'الأعلام'],

            'general.actions.labor_prices' => ['en' => 'Labor Prices', 'ar' => 'أسعار العمل'],
            'general.actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'general.actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'general.actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'general.actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'general.actions.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'general.actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'general.actions.details' => ['en' => 'Details', 'ar' => 'التفاصيل'],
            'general.actions.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'general.actions.export' => ['en' => 'Export', 'ar' => 'تصدير'],
            'general.all' => ['en' => 'All', 'ar' => 'الكل'],
            'status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'status.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],
            'fields.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'fields.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
        ];

        $created = 0;
        foreach ($translations as $key => $values) {
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

        $this->command->info("✓ Recruitment translations created: {$created} entries");
    }
}
