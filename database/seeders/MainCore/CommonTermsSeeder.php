<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class CommonTermsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping common terms translations.');
            return;
        }

        $translations = [
            // Common Terms
            'common.select' => ['en' => 'Select', 'ar' => 'اختر'],
            'common.select_all' => ['en' => 'Select All', 'ar' => 'تحديد الكل'],
            'common.none' => ['en' => 'None', 'ar' => 'لا شيء'],
            'common.all' => ['en' => 'All', 'ar' => 'الكل'],
            'common.yes' => ['en' => 'Yes', 'ar' => 'نعم'],
            'common.no' => ['en' => 'No', 'ar' => 'لا'],
            'common.ok' => ['en' => 'OK', 'ar' => 'موافق'],
            'common.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'common.confirm' => ['en' => 'Confirm', 'ar' => 'تأكيد'],
            'common.close' => ['en' => 'Close', 'ar' => 'إغلاق'],
            'common.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'common.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'common.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'common.view' => ['en' => 'View', 'ar' => 'عرض'],
            'common.create' => ['en' => 'Create', 'ar' => 'إنشاء'],
            'common.update' => ['en' => 'Update', 'ar' => 'تحديث'],
            'common.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'common.filter' => ['en' => 'Filter', 'ar' => 'تصفية'],
            'common.clear' => ['en' => 'Clear', 'ar' => 'مسح'],
            'common.reset' => ['en' => 'Reset', 'ar' => 'إعادة تعيين'],

            // Status Terms
            'common.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'common.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],
            'common.enabled' => ['en' => 'Enabled', 'ar' => 'مفعل'],
            'common.disabled' => ['en' => 'Disabled', 'ar' => 'معطل'],
            'common.published' => ['en' => 'Published', 'ar' => 'منشور'],
            'common.unpublished' => ['en' => 'Unpublished', 'ar' => 'غير منشور'],
            'common.archived' => ['en' => 'Archived', 'ar' => 'مؤرشف'],
            'common.draft' => ['en' => 'Draft', 'ar' => 'مسودة'],

            // Date/Time Terms
            'common.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'common.time' => ['en' => 'Time', 'ar' => 'الوقت'],
            'common.from_date' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'common.to_date' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'common.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'common.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'common.today' => ['en' => 'Today', 'ar' => 'اليوم'],
            'common.yesterday' => ['en' => 'Yesterday', 'ar' => 'أمس'],
            'common.tomorrow' => ['en' => 'Tomorrow', 'ar' => 'غداً'],

            // Common Labels
            'common.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'common.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'common.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'common.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'common.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'common.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'common.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'common.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'common.remarks' => ['en' => 'Remarks', 'ar' => 'ملاحظات'],

            // Messages
            'common.success' => ['en' => 'Success', 'ar' => 'نجح'],
            'common.error' => ['en' => 'Error', 'ar' => 'خطأ'],
            'common.warning' => ['en' => 'Warning', 'ar' => 'تحذير'],
            'common.info' => ['en' => 'Info', 'ar' => 'معلومات'],
            'common.loading' => ['en' => 'Loading...', 'ar' => 'جاري التحميل...'],
            'common.processing' => ['en' => 'Processing...', 'ar' => 'جاري المعالجة...'],
            'common.saved' => ['en' => 'Saved', 'ar' => 'تم الحفظ'],
            'common.deleted' => ['en' => 'Deleted', 'ar' => 'تم الحذف'],
            'common.updated' => ['en' => 'Updated', 'ar' => 'تم التحديث'],
            'common.created' => ['en' => 'Created', 'ar' => 'تم الإنشاء'],

            // Table Terms
            'common.records' => ['en' => 'Records', 'ar' => 'السجلات'],
            'common.record' => ['en' => 'Record', 'ar' => 'سجل'],
            'common.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'common.count' => ['en' => 'Count', 'ar' => 'العدد'],
            'common.actions' => ['en' => 'Actions', 'ar' => 'الإجراءات'],
            'common.no_records' => ['en' => 'No records found', 'ar' => 'لا توجد سجلات'],
            'common.no_data' => ['en' => 'No data available', 'ar' => 'لا توجد بيانات متاحة'],
            'common.showing' => ['en' => 'Showing', 'ar' => 'عرض'],
            'common.of' => ['en' => 'of', 'ar' => 'من'],
            'common.results' => ['en' => 'results', 'ar' => 'نتيجة'],

            // Form Terms
            'common.required' => ['en' => 'Required', 'ar' => 'مطلوب'],
            'common.optional' => ['en' => 'Optional', 'ar' => 'اختياري'],
            'common.placeholder' => ['en' => 'Enter...', 'ar' => 'أدخل...'],
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

        $this->command->info("Common terms translations seeded: {$created} created, {$updated} updated.");
    }
}

