<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class VoucherAndJournalTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic  = Language::where('code', 'ar')->first();

        if (! $english || ! $arabic) {
            $this->command->warn('English or Arabic language not found. Skipping translations.');
            return;
        }

        $translations = [

            // =========================
            // MENU (Sidebar)
            // =========================
            'menu.accounting.journals' => [
                'en' => 'Journals',
                'ar' => 'اليوميات',
            ],
            'menu.accounting.voucher_signatures' => [
                'en' => 'Voucher Signatures',
                'ar' => 'توقيعات السندات',
            ],

            // =========================
            // COMMON
            // =========================
            'common.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'common.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'common.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'common.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],
            'common.activate' => [
                'en' => 'Activate',
                'ar' => 'تفعيل',
            ],
            'common.deactivate' => [
                'en' => 'Deactivate',
                'ar' => 'إلغاء التفعيل',
            ],

            'tables.common.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],

            // =========================
            // JOURNALS (Form)
            // =========================
            'forms.journals.code' => [
                'en' => 'Journal Code',
                'ar' => 'رمز اليومية',
            ],
            'forms.journals.name' => [
                'en' => 'Journal Name',
                'ar' => 'اسم اليومية',
            ],
            'forms.journals.type' => [
                'en' => 'Journal Type',
                'ar' => 'نوع اليومية',
            ],
            'forms.journals.code_helper' => [
                'en' => 'Unique code for the journal',
                'ar' => 'رمز فريد لليومية',
            ],

            // Journals types
            'journals.types.general' => [
                'en' => 'General Journal',
                'ar' => 'يومية عامة',
            ],
            'journals.types.bank' => [
                'en' => 'Bank Journal',
                'ar' => 'يومية بنكية',
            ],
            'journals.types.cash' => [
                'en' => 'Cash Journal',
                'ar' => 'يومية نقدية',
            ],
            'journals.types.purchase' => [
                'en' => 'Purchase Journal',
                'ar' => 'يومية مشتريات',
            ],
            'journals.types.sales' => [
                'en' => 'Sales Journal',
                'ar' => 'يومية مبيعات',
            ],

            // Journals table
            'tables.journals.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.journals.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.journals.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.journals.entries' => [
                'en' => 'Entries',
                'ar' => 'القيود',
            ],

            // =========================
            // VOUCHER SIGNATURES (Form)
            // =========================
            'vouchers.signatures.basic_info' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'vouchers.signatures.signature_image' => [
                'en' => 'Signature Image',
                'ar' => 'صورة التوقيع',
            ],
            'vouchers.signatures.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'vouchers.signatures.title' => [
                'en' => 'Title',
                'ar' => 'الوظيفة',
            ],
            'vouchers.signatures.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'vouchers.signatures.sort_order' => [
                'en' => 'Sort Order',
                'ar' => 'ترتيب العرض',
            ],
            'vouchers.signatures.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'vouchers.signatures.image' => [
                'en' => 'Signature Image',
                'ar' => 'صورة التوقيع',
            ],

            'vouchers.signatures.name_helper' => [
                'en' => 'Display name for the signature (e.g., "Ahmed Nour")',
                'ar' => 'اسم يظهر في التوقيعات (مثال: "أحمد نور")',
            ],
            'vouchers.signatures.edit_title' => ['en' => 'Edit Voucher Signature', 'ar' => 'تعديل توقيع السند'],

'common.edit'   => ['en' => 'Edit', 'ar' => 'تعديل'],
'common.delete' => ['en' => 'Delete', 'ar' => 'حذف'],

            'vouchers.signatures.title_helper' => [
                'en' => 'Role or title (e.g., "Accountant", "Manager")',
                'ar' => 'المسمى الوظيفي (مثال: "محاسب", "مدير")',
            ],
            'vouchers.signatures.type_helper' => [
                'en' => 'Restrict signature to specific voucher types. Leave empty for both.',
                'ar' => 'تحديد التوقيع لنوع سند معيّن. اتركه فارغًا ليظهر في الكل.',
            ],
            'vouchers.signatures.sort_order_helper' => [
                'en' => 'Lower numbers appear first in selection lists',
                'ar' => 'الأرقام الأصغر تظهر أولًا في القوائم',
            ],
            'vouchers.signatures.image_helper' => [
                'en' => 'Upload signature image or stamp. Recommended size: 200x100px',
                'ar' => 'ارفع صورة التوقيع أو الختم. المقاس المقترح: 200×100',
            ],

            // Voucher signatures types
            'vouchers.signatures.type_both' => [
                'en' => 'Both (Payment & Receipt)',
                'ar' => 'كلاهما (صرف وقبض)',
            ],
            'vouchers.signatures.type_receipt' => [
                'en' => 'Receipt Only',
                'ar' => 'قبض فقط',
            ],
            'vouchers.signatures.type_payment' => [
                'en' => 'Payment Only',
                'ar' => 'صرف فقط',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            $en = Translation::updateOrCreate(
                ['key' => $key, 'group' => 'dashboard', 'language_id' => $english->id],
                ['value' => $values['en']]
            );

            $ar = Translation::updateOrCreate(
                ['key' => $key, 'group' => 'dashboard', 'language_id' => $arabic->id],
                ['value' => $values['ar']]
            );

            if ($en->wasRecentlyCreated || $ar->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Voucher & Journal translations seeded: {$created} created, {$updated} updated.");
    }
}
