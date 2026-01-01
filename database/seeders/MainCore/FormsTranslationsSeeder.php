<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class FormsTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping forms translations.');
            return;
        }

        $translations = [
            // Common Form Labels
            'forms.common.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.common.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.common.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'forms.common.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.common.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'forms.common.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'forms.common.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'forms.common.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'forms.common.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],

            // Form Placeholders
            'forms.placeholders.name' => ['en' => 'Enter name', 'ar' => 'أدخل الاسم'],
            'forms.placeholders.code' => ['en' => 'Enter code', 'ar' => 'أدخل الرمز'],
            'forms.placeholders.description' => ['en' => 'Enter description', 'ar' => 'أدخل الوصف'],
            'forms.placeholders.search' => ['en' => 'Search...', 'ar' => 'بحث...'],
            'forms.placeholders.select' => ['en' => 'Select...', 'ar' => 'اختر...'],
            'forms.placeholders.date' => ['en' => 'Select date', 'ar' => 'اختر التاريخ'],
            'forms.placeholders.email' => ['en' => 'Enter email address', 'ar' => 'أدخل عنوان البريد الإلكتروني'],
            'forms.placeholders.phone' => ['en' => 'Enter phone number', 'ar' => 'أدخل رقم الهاتف'],

            // Validation Messages
            'forms.validation.required' => ['en' => 'This field is required', 'ar' => 'هذا الحقل مطلوب'],
            'forms.validation.email' => ['en' => 'Please enter a valid email address', 'ar' => 'يرجى إدخال عنوان بريد إلكتروني صحيح'],
            'forms.validation.numeric' => ['en' => 'This field must be a number', 'ar' => 'يجب أن يكون هذا الحقل رقماً'],
            'forms.validation.min' => ['en' => 'This field must be at least :min characters', 'ar' => 'يجب أن يكون هذا الحقل على الأقل :min أحرف'],
            'forms.validation.max' => ['en' => 'This field must not exceed :max characters', 'ar' => 'يجب ألا يتجاوز هذا الحقل :max حرف'],
            'forms.validation.unique' => ['en' => 'This value already exists', 'ar' => 'هذه القيمة موجودة بالفعل'],
            'forms.validation.confirmed' => ['en' => 'The confirmation does not match', 'ar' => 'التأكيد غير متطابق'],

            // Form Sections
            'forms.sections.basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'forms.sections.additional_info' => ['en' => 'Additional Information', 'ar' => 'معلومات إضافية'],
            'forms.sections.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'forms.sections.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],
            'forms.sections.contact_info' => ['en' => 'Contact Information', 'ar' => 'معلومات الاتصال'],
            'forms.sections.address' => ['en' => 'Address', 'ar' => 'العنوان'],

            // Helper Texts
            'forms.helpers.required_field' => ['en' => 'This field is required', 'ar' => 'هذا الحقل مطلوب'],
            'forms.helpers.optional_field' => ['en' => 'This field is optional', 'ar' => 'هذا الحقل اختياري'],
            'forms.helpers.unique_code' => ['en' => 'Must be unique', 'ar' => 'يجب أن يكون فريداً'],
            'forms.helpers.max_length' => ['en' => 'Maximum :max characters', 'ar' => 'الحد الأقصى :max حرف'],
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

        $this->command->info("Forms translations seeded: {$created} created, {$updated} updated.");
    }
}

