<?php

namespace Database\Seeders;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class ClientsTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Clients translations.');
            return;
        }

        $this->command->info('Creating Clients module translations...');

        $translations = [
            'general.clients.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'general.clients.add_client' => ['en' => 'Add Client', 'ar' => 'إضافة عميل'],
            'general.clients.basic_data' => ['en' => 'Basic Data', 'ar' => 'البيانات الأساسية'],
            'general.clients.national_address' => ['en' => 'National Address', 'ar' => 'العنوان الوطني'],
            'general.clients.housing_data' => ['en' => 'Housing Data', 'ar' => 'بيانات السكن'],
            'general.clients.other_data' => ['en' => 'Other Data', 'ar' => 'بيانات أخرى'],
            'general.clients.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'general.clients.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'general.clients.client_code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'general.clients.national_id' => ['en' => 'National ID', 'ar' => 'الهوية الوطنية'],
            'general.clients.mobile' => ['en' => 'Mobile', 'ar' => 'الجوال'],
            'general.clients.mobile2' => ['en' => 'Mobile 2', 'ar' => 'الجوال 2'],
            'general.clients.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'general.clients.birth_date' => ['en' => 'Birth Date', 'ar' => 'تاريخ الميلاد'],
            'general.clients.marital_status' => ['en' => 'Marital Status', 'ar' => 'الحالة الاجتماعية'],
            'general.clients.single' => ['en' => 'Single', 'ar' => 'أعزب'],
            'general.clients.married' => ['en' => 'Married', 'ar' => 'متزوج'],
            'general.clients.divorced' => ['en' => 'Divorced', 'ar' => 'مطلق'],
            'general.clients.widowed' => ['en' => 'Widowed', 'ar' => 'أرمل'],
            'general.clients.classification' => ['en' => 'Classification', 'ar' => 'التصنيف'],
            'general.clients.new' => ['en' => 'New', 'ar' => 'جديد'],
            'general.clients.vip' => ['en' => 'VIP', 'ar' => 'مميز'],
            'general.clients.blocked' => ['en' => 'Blocked', 'ar' => 'محظور'],
            'general.clients.building_no' => ['en' => 'Building No', 'ar' => 'رقم المبنى'],
            'general.clients.street_name' => ['en' => 'Street Name', 'ar' => 'اسم الشارع'],
            'general.clients.city_name' => ['en' => 'City Name', 'ar' => 'اسم المدينة'],
            'general.clients.district_name' => ['en' => 'District Name', 'ar' => 'اسم الحي'],
            'general.clients.postal_code' => ['en' => 'Postal Code', 'ar' => 'الرمز البريدي'],
            'general.clients.additional_no' => ['en' => 'Additional No', 'ar' => 'الرقم الإضافي'],
            'general.clients.unit_no' => ['en' => 'Unit No', 'ar' => 'رقم الوحدة'],
            'general.clients.building_no_en' => ['en' => 'Building No (English)', 'ar' => 'رقم المبنى (إنجليزي)'],
            'general.clients.street_name_en' => ['en' => 'Street Name (English)', 'ar' => 'اسم الشارع (إنجليزي)'],
            'general.clients.city_name_en' => ['en' => 'City Name (English)', 'ar' => 'اسم المدينة (إنجليزي)'],
            'general.clients.district_name_en' => ['en' => 'District Name (English)', 'ar' => 'اسم الحي (إنجليزي)'],
            'general.clients.unit_no_en' => ['en' => 'Unit No (English)', 'ar' => 'رقم الوحدة (إنجليزي)'],
            'general.clients.full_address_ar' => ['en' => 'Full Address (Arabic)', 'ar' => 'العنوان الكامل (عربي)'],
            'general.clients.full_address_en' => ['en' => 'Full Address (English)', 'ar' => 'العنوان الكامل (إنجليزي)'],
            'general.clients.generate_address' => ['en' => 'Generate Address', 'ar' => 'إنشاء العنوان'],
            'general.clients.housing_type' => ['en' => 'Housing Type', 'ar' => 'نوع السكن'],
            'general.clients.villa' => ['en' => 'Villa', 'ar' => 'فيلا'],
            'general.clients.building' => ['en' => 'Building', 'ar' => 'مبنى'],
            'general.clients.apartment' => ['en' => 'Apartment', 'ar' => 'شقة'],
            'general.clients.farm' => ['en' => 'Farm', 'ar' => 'مزرعة'],
            'general.clients.id_image' => ['en' => 'ID Image', 'ar' => 'صورة الهوية'],
            'general.clients.other_document' => ['en' => 'Other Document', 'ar' => 'مستند آخر'],
            'general.clients.source' => ['en' => 'Source', 'ar' => 'المصدر'],
            'general.clients.office_referral' => ['en' => 'Office Referral', 'ar' => 'إحالة المكتب'],
            'general.clients.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'general.clients.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'general.clients.download_template' => ['en' => 'Download Template', 'ar' => 'تحميل القالب'],
            'general.clients.import' => ['en' => 'Import Excel', 'ar' => 'استيراد Excel'],
            'general.clients.excel_file' => ['en' => 'Excel File', 'ar' => 'ملف Excel'],
            'general.clients.import_success' => ['en' => 'Clients imported successfully', 'ar' => 'تم استيراد العملاء بنجاح'],
            'general.clients.columns_added' => ['en' => 'New columns added', 'ar' => 'تم إضافة أعمدة جديدة'],
            'general.clients.import_complete' => ['en' => 'Import Complete', 'ar' => 'اكتمل الاستيراد'],
            'general.clients.import_errors' => ['en' => 'Import Errors', 'ar' => 'أخطاء الاستيراد'],
            'general.clients.import_failed' => ['en' => 'Import Failed', 'ar' => 'فشل الاستيراد'],
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

        $this->command->info("✓ Clients translations created: {$created} entries");
    }
}
