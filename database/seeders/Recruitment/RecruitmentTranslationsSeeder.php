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
            'navigation.recruitment_agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'navigation.recruitment_agent_prices' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            'recruitment.agents.title' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'recruitment.agent_prices.title' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            
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
            'recruitment.fields.passport_issue_place' => ['en' => 'Passport Issue Place', 'ar' => 'مكان إصدار الجواز'],
            'recruitment.fields.bank_sender' => ['en' => 'Bank Sender', 'ar' => 'البنك المرسل'],
            'recruitment.fields.account_number' => ['en' => 'Account Number', 'ar' => 'رقم الحساب'],
            'recruitment.fields.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'recruitment.fields.username' => ['en' => 'Username', 'ar' => 'اسم المستخدم'],
            'recruitment.fields.password' => ['en' => 'Password', 'ar' => 'كلمة المرور'],
            'recruitment.fields.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'recruitment.fields.responsible_name' => ['en' => 'Responsible Name', 'ar' => 'اسم المسؤول'],
            
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
            
            'general.actions.labor_prices' => ['en' => 'Labor Prices', 'ar' => 'أسعار العمل'],
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
