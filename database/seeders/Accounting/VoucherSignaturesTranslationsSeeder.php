<?php

namespace Database\Seeders\Accounting;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class VoucherSignaturesTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping translations.');
            return;
        }

        $translations = [
            // Voucher Actions
            'vouchers.actions.print_voucher' => ['en' => 'Print Voucher', 'ar' => 'طباعة السند'],
            'vouchers.actions.export_pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],
            'vouchers.actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'vouchers.actions.export' => ['en' => 'Export', 'ar' => 'تصدير'],

            // Signature Selection
            'vouchers.signatures.choose_count' => ['en' => 'Number of Signatures', 'ar' => 'عدد التوقيعات'],
            'vouchers.signatures.choose_count_helper' => ['en' => 'Select how many signatures to include (0-6)', 'ar' => 'اختر عدد التوقيعات المراد تضمينها (0-6)'],
            'vouchers.signatures.choose_signatures' => ['en' => 'Select Signatures', 'ar' => 'اختر التوقيعات'],
            'vouchers.signatures.signature_1' => ['en' => 'Signature 1', 'ar' => 'التوقيع الأول'],
            'vouchers.signatures.signature_2' => ['en' => 'Signature 2', 'ar' => 'التوقيع الثاني'],
            'vouchers.signatures.signature_3' => ['en' => 'Signature 3', 'ar' => 'التوقيع الثالث'],
            'vouchers.signatures.signature_4' => ['en' => 'Signature 4', 'ar' => 'التوقيع الرابع'],
            'vouchers.signatures.signature_5' => ['en' => 'Signature 5', 'ar' => 'التوقيع الخامس'],
            'vouchers.signatures.signature_6' => ['en' => 'Signature 6', 'ar' => 'التوقيع السادس'],
            'vouchers.signatures.section_title' => ['en' => 'Signatures', 'ar' => 'التوقيعات'],
            'vouchers.signatures.no_duplicates' => ['en' => 'Duplicate signatures are not allowed', 'ar' => 'التوقيعات المكررة غير مسموحة'],

            // Signature Management
            'vouchers.signatures.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'vouchers.signatures.name_helper' => ['en' => 'Display name for the signature (e.g., "Ahmed Nour")', 'ar' => 'اسم العرض للتوقيع (مثل: "أحمد نور")'],
            'vouchers.signatures.title' => ['en' => 'Title', 'ar' => 'المسمى الوظيفي'],
            'vouchers.signatures.title_helper' => ['en' => 'Role or title (e.g., "Accountant", "Manager")', 'ar' => 'الدور أو المسمى الوظيفي (مثل: "محاسب"، "مدير")'],
            'vouchers.signatures.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'vouchers.signatures.type_helper' => ['en' => 'Restrict signature to specific voucher types. Leave empty for both.', 'ar' => 'تقييد التوقيع لأنواع معينة من السندات. اتركه فارغاً لكلا النوعين.'],
            'vouchers.signatures.type_both' => ['en' => 'Both (Payment & Receipt)', 'ar' => 'كلاهما (صرف وقبض)'],
            'vouchers.signatures.type_receipt' => ['en' => 'Receipt Only', 'ar' => 'سند قبض فقط'],
            'vouchers.signatures.type_payment' => ['en' => 'Payment Only', 'ar' => 'سند صرف فقط'],
            'vouchers.signatures.image' => ['en' => 'Signature Image', 'ar' => 'صورة التوقيع'],
            'vouchers.signatures.image_helper' => ['en' => 'Upload signature image or stamp. Recommended size: 200x100px', 'ar' => 'قم برفع صورة التوقيع أو الختم. الحجم الموصى به: 200x100 بكسل'],
            'vouchers.signatures.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'vouchers.signatures.sort_order' => ['en' => 'Sort Order', 'ar' => 'ترتيب العرض'],
            'vouchers.signatures.sort_order_helper' => ['en' => 'Lower numbers appear first in selection lists', 'ar' => 'الأرقام الأقل تظهر أولاً في قوائم الاختيار'],
            'vouchers.signatures.basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'vouchers.signatures.signature_image' => ['en' => 'Signature Image', 'ar' => 'صورة التوقيع'],
            'vouchers.signatures.date' => ['en' => 'Date', 'ar' => 'التاريخ'],

            // Voucher Print Template
            'vouchers.payment_voucher' => ['en' => 'Payment Voucher', 'ar' => 'سند صرف'],
            'vouchers.receipt_voucher' => ['en' => 'Receipt Voucher', 'ar' => 'سند قبض'],
            'vouchers.voucher_number' => ['en' => 'Voucher Number', 'ar' => 'رقم السند'],
            'vouchers.voucher_date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'vouchers.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'vouchers.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'vouchers.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'vouchers.amount_in_words' => ['en' => 'Amount in Words', 'ar' => 'المبلغ كتابة'],
            'vouchers.generated_on' => ['en' => 'Generated on', 'ar' => 'تم الإنشاء في'],
            'vouchers.created_by' => ['en' => 'Created by', 'ar' => 'تم الإنشاء بواسطة'],

            // Menu
            'menu.accounting.voucher_signatures' => ['en' => 'Voucher Signatures', 'ar' => 'توقيعات السندات'],
        ];

        foreach ($translations as $key => $values) {
            // English translation
            Translation::updateOrCreate(
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
            Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );
        }

        $this->command->info('Voucher Signatures translations seeded successfully!');
    }
}

