<?php

namespace Database\Seeders;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ServiceTransferTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Service Transfer translations...');

        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping database translations.');
        }

        $translations = [
            // Sidebar navigation translations
            'sidebar.servicetransfer' => ['en' => 'Service Transfers', 'ar' => 'طلبات نقل الخدمات'],
            'sidebar.servicetransferrequestsreport' => ['en' => 'Service Transfer Requests Report', 'ar' => 'تقرير طلبات نقل الخدمات'],
            'sidebar.servicetransferpaymentsreport' => ['en' => 'Service Transfer Payments Report', 'ar' => 'تقرير المدفوعات - نقل الخدمات'],
            'general.service_transfer' => ['en' => 'Service Transfer', 'ar' => 'نقل الخدمات'],
            'general.service_transfer_dashboard' => ['en' => 'Service Transfer Dashboard', 'ar' => 'لوحة نقل الخدمات'],
            'general.create_service_transfer' => ['en' => 'Create Service Transfer Request', 'ar' => 'إنشاء طلب نقل خدمة'],
            'general.service_transfers' => ['en' => 'Service Transfers', 'ar' => 'طلبات نقل الخدمات'],
            'general.service_transfer_reports' => ['en' => 'Service Transfer Reports', 'ar' => 'تقارير نقل الخدمات'],
            'general.service_transfer_payments_report' => ['en' => 'Service Transfer Payments Report', 'ar' => 'تقرير المدفوعات - نقل الخدمات'],
            'general.request_no' => ['en' => 'Request Number', 'ar' => 'رقم الطلب'],
            'general.request_date' => ['en' => 'Request Date', 'ar' => 'تاريخ الطلب'],
            'general.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'general.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'general.worker' => ['en' => 'Worker', 'ar' => 'العاملة'],
            'general.nationality' => ['en' => 'Nationality', 'ar' => 'الدولة'],
            'general.package' => ['en' => 'Package', 'ar' => 'الباقة'],
            'general.base_price' => ['en' => 'Base Price', 'ar' => 'السعر الأساسي'],
            'general.external_cost' => ['en' => 'External Cost', 'ar' => 'التكاليف الخارجية'],
            'general.government_fees' => ['en' => 'Government Fees', 'ar' => 'الرسوم الحكومية'],
            'general.tax_percent' => ['en' => 'Tax Percent', 'ar' => 'نسبة الضريبة'],
            'general.tax_value' => ['en' => 'Tax Value', 'ar' => 'قيمة الضريبة'],
            'general.discount_percent' => ['en' => 'Discount Percent', 'ar' => 'نسبة الخصم'],
            'general.discount_value' => ['en' => 'Discount Value', 'ar' => 'قيمة الخصم'],
            'general.discount_reason' => ['en' => 'Discount Reason', 'ar' => 'سبب الخصم'],
            'general.total_amount' => ['en' => 'Total Amount', 'ar' => 'المبلغ الإجمالي'],
            'general.payment_status' => ['en' => 'Payment Status', 'ar' => 'حالة الدفع'],
            'general.request_status' => ['en' => 'Request Status', 'ar' => 'حالة الطلب'],
            'general.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'general.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'general.reset' => ['en' => 'Reset', 'ar' => 'إعادة تعيين'],
            'general.active_requests' => ['en' => 'Active Requests', 'ar' => 'الطلبات النشطة'],
            'general.refunded_requests' => ['en' => 'Refunded Requests', 'ar' => 'الطلبات المستردة'],
            'general.archive' => ['en' => 'Archive', 'ar' => 'أرشفة'],
            'general.payments' => ['en' => 'Payments', 'ar' => 'المدفوعات'],
            'general.payment_no' => ['en' => 'Payment Number', 'ar' => 'رقم الدفعة'],
            'general.payment_date' => ['en' => 'Payment Date', 'ar' => 'تاريخ الدفع'],
            'general.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'general.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'general.unpaid' => ['en' => 'Unpaid', 'ar' => 'غير مدفوع'],
            'general.partial' => ['en' => 'Partial', 'ar' => 'جزئي'],
            'general.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'general.refunded' => ['en' => 'Refunded', 'ar' => 'مسترد'],
            'general.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'general.archived' => ['en' => 'Archived', 'ar' => 'مؤرشف'],
            'general.created_at' => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],
            'general.created_by' => ['en' => 'Created By', 'ar' => 'أنشئ بواسطة'],
        ];

        $langDir = public_path('lang');
        if (!File::exists($langDir)) {
            File::makeDirectory($langDir, 0755, true);
        }

        $arFile = $langDir . '/ar.json';
        $enFile = $langDir . '/en.json';

        $arData = [];
        $enData = [];

        if (File::exists($arFile)) {
            $arData = json_decode(File::get($arFile), true) ?? [];
        }

        if (File::exists($enFile)) {
            $enData = json_decode(File::get($enFile), true) ?? [];
        }

        foreach ($translations as $key => $values) {
            $keys = explode('.', $key);
            $lastKey = array_pop($keys);

            $arCurrent = &$arData;
            $enCurrent = &$enData;

            foreach ($keys as $k) {
                if (!isset($arCurrent[$k])) {
                    $arCurrent[$k] = [];
                }
                if (!isset($enCurrent[$k])) {
                    $enCurrent[$k] = [];
                }
                $arCurrent = &$arCurrent[$k];
                $enCurrent = &$enCurrent[$k];
            }

            $arCurrent[$lastKey] = $values['ar'];
            $enCurrent[$lastKey] = $values['en'];
        }

        File::put($arFile, json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        File::put($enFile, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->command->info('✓ Service Transfer translations created in public/lang/ar.json and public/lang/en.json');

        // Seed translations to database
        if ($english && $arabic) {
            $this->command->info('Seeding Service Transfer translations to database...');
            
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

            $this->command->info("✓ Service Transfer translations seeded to database: {$created} created, {$updated} updated.");
        }
    }
}
