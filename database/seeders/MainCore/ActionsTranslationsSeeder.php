<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class ActionsTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping actions translations.');
            return;
        }

        $translations = [
            // Common Actions
            'actions.create' => ['en' => 'Create', 'ar' => 'إنشاء'],
            'actions.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'actions.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'actions.update' => ['en' => 'Update', 'ar' => 'تحديث'],
            'actions.submit' => ['en' => 'Submit', 'ar' => 'إرسال'],
            'actions.reset' => ['en' => 'Reset', 'ar' => 'إعادة تعيين'],
            'actions.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'actions.filter' => ['en' => 'Filter', 'ar' => 'تصفية'],
            'actions.clear' => ['en' => 'Clear', 'ar' => 'مسح'],
            'actions.close' => ['en' => 'Close', 'ar' => 'إغلاق'],
            'actions.back' => ['en' => 'Back', 'ar' => 'رجوع'],
            'actions.next' => ['en' => 'Next', 'ar' => 'التالي'],
            'actions.previous' => ['en' => 'Previous', 'ar' => 'السابق'],
            'actions.confirm' => ['en' => 'Confirm', 'ar' => 'تأكيد'],
            'actions.approve' => ['en' => 'Approve', 'ar' => 'موافقة'],
            'actions.reject' => ['en' => 'Reject', 'ar' => 'رفض'],
            'actions.view' => ['en' => 'View', 'ar' => 'عرض'],
            'actions.show' => ['en' => 'Show', 'ar' => 'إظهار'],
            'actions.hide' => ['en' => 'Hide', 'ar' => 'إخفاء'],
            'actions.export' => ['en' => 'Export', 'ar' => 'تصدير'],
            'actions.import' => ['en' => 'Import', 'ar' => 'استيراد'],
            'actions.download' => ['en' => 'Download', 'ar' => 'تحميل'],
            'actions.upload' => ['en' => 'Upload', 'ar' => 'رفع'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.preview' => ['en' => 'Preview', 'ar' => 'معاينة'],
            'actions.refresh' => ['en' => 'Refresh', 'ar' => 'تحديث'],
            'actions.reload' => ['en' => 'Reload', 'ar' => 'إعادة تحميل'],

            // Export Actions
            'actions.export_excel' => ['en' => 'Export Excel', 'ar' => 'تصدير Excel'],
            'actions.export_pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],
            'actions.export_csv' => ['en' => 'Export CSV', 'ar' => 'تصدير CSV'],
            'actions.export_json' => ['en' => 'Export JSON', 'ar' => 'تصدير JSON'],

            // Bulk Actions
            'actions.bulk_delete' => ['en' => 'Delete Selected', 'ar' => 'حذف المحدد'],
            'actions.bulk_edit' => ['en' => 'Edit Selected', 'ar' => 'تعديل المحدد'],
            'actions.select_all' => ['en' => 'Select All', 'ar' => 'تحديد الكل'],
            'actions.deselect_all' => ['en' => 'Deselect All', 'ar' => 'إلغاء تحديد الكل'],

            // Table Actions
            'actions.new_record' => ['en' => 'New Record', 'ar' => 'سجل جديد'],
            'actions.edit_record' => ['en' => 'Edit Record', 'ar' => 'تعديل السجل'],
            'actions.delete_record' => ['en' => 'Delete Record', 'ar' => 'حذف السجل'],
            'actions.view_record' => ['en' => 'View Record', 'ar' => 'عرض السجل'],
            'actions.duplicate' => ['en' => 'Duplicate', 'ar' => 'نسخ'],

            // Status Actions
            'actions.activate' => ['en' => 'Activate', 'ar' => 'تفعيل'],
            'actions.deactivate' => ['en' => 'Deactivate', 'ar' => 'تعطيل'],
            'actions.enable' => ['en' => 'Enable', 'ar' => 'تفعيل'],
            'actions.disable' => ['en' => 'Disable', 'ar' => 'تعطيل'],
            'actions.publish' => ['en' => 'Publish', 'ar' => 'نشر'],
            'actions.unpublish' => ['en' => 'Unpublish', 'ar' => 'إلغاء النشر'],
            'actions.archive' => ['en' => 'Archive', 'ar' => 'أرشفة'],
            'actions.restore' => ['en' => 'Restore', 'ar' => 'استعادة'],
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

        $this->command->info("Actions translations seeded: {$created} created, {$updated} updated.");
    }
}

