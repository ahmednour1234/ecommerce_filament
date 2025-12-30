<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnsureArabicTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder ensures all translations in the system have Arabic versions.
     * It will:
     * 1. Find all existing translations
     * 2. Check which ones are missing Arabic versions
     * 3. Create Arabic translations for missing ones (using English as fallback or auto-translate)
     */
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Ensuring all translations have Arabic versions...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        // First, ensure we have all translations from seeders
        $this->command->info('Step 1: Running existing translation seeders...');
        $this->call(DashboardTranslationSeeder::class);
        $this->call(\Database\Seeders\Accounting\AccountingTranslationsSeeder::class);
        $this->command->info('✓ Existing translation seeders completed.');
        $this->command->newLine();

        $this->command->info('Step 2: Checking for missing Arabic translations...');

        $arabic = Language::where('code', 'ar')->first();
        $english = Language::where('code', 'en')->first();

        if (!$arabic) {
            $this->command->error('❌ Arabic language not found! Please run LanguageSeeder first.');
            return;
        }

        if (!$english) {
            $this->command->error('❌ English language not found! Please run LanguageSeeder first.');
            return;
        }

        // Get all unique translation keys and groups
        $allTranslations = Translation::select('key', 'group')
            ->distinct()
            ->get();

        $this->command->info("Found {$allTranslations->count()} unique translation keys.");
        $this->command->newLine();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($allTranslations as $translationData) {
            $key = $translationData->key;
            $group = $translationData->group;

            // Check if Arabic translation exists
            $arabicTranslation = Translation::where('key', $key)
                ->where('group', $group)
                ->where('language_id', $arabic->id)
                ->first();

            if ($arabicTranslation) {
                $skipped++;
                continue;
            }

            // Get English translation as fallback
            $englishTranslation = Translation::where('key', $key)
                ->where('group', $group)
                ->where('language_id', $english->id)
                ->first();

            // If no English translation, try to get any other language as reference
            if (!$englishTranslation) {
                $anyTranslation = Translation::where('key', $key)
                    ->where('group', $group)
                    ->first();
                
                if ($anyTranslation) {
                    // Use the key as the value if no translation found
                    $value = $key;
                } else {
                    $skipped++;
                    $this->command->warn("⚠ Skipping '{$key}' (group: {$group}) - no reference translation found.");
                    continue;
                }
            } else {
                $value = $englishTranslation->value;
            }

            // Create Arabic translation
            // For now, we'll use the English value with a note that it needs translation
            // In a real scenario, you might want to use a translation API or manual translations
            $arabicValue = $this->getArabicTranslation($key, $value, $group);

            Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => $group,
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $arabicValue,
                ]
            );

            if ($arabicTranslation) {
                $updated++;
            } else {
                $created++;
            }
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("✓ Created {$created} new Arabic translations");
        $this->command->info("✓ Updated {$updated} existing Arabic translations");
        $this->command->info("⊘ Skipped {$skipped} translations (already exist or no reference)");
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        // Clear cache
        \Illuminate\Support\Facades\Cache::flush();
        $this->command->info('✓ Cache cleared.');
    }

    /**
     * Get Arabic translation for a key
     * This method uses predefined translations from seeders
     */
    private function getArabicTranslation(string $key, string $englishValue, string $group): string
    {
        // First, check if we have a predefined Arabic translation in our seeders
        $predefinedTranslations = $this->getPredefinedArabicTranslations();

        // Check by key first (exact match)
        if (isset($predefinedTranslations[$key])) {
            return $predefinedTranslations[$key];
        }

        // Try to find similar keys (for variations)
        foreach ($predefinedTranslations as $predefinedKey => $arabicValue) {
            // Check if the key starts with the same prefix
            $keyParts = explode('.', $key);
            $predefinedParts = explode('.', $predefinedKey);
            
            if (count($keyParts) >= 2 && count($predefinedParts) >= 2) {
                if ($keyParts[0] === $predefinedParts[0] && $keyParts[1] === $predefinedParts[1]) {
                    // Same group and similar structure, use the Arabic translation
                    return $arabicValue;
                }
            }
        }

        // If no predefined translation found, return the English value
        // This allows the system to work while translations are being added
        // In production, you might want to:
        // 1. Use a translation API (Google Translate, DeepL, etc.)
        // 2. Use a lookup table
        // 3. Leave it for manual translation via the admin panel
        return $englishValue; // Use English as fallback until Arabic is added
    }

    /**
     * Get predefined Arabic translations from existing seeders
     * This consolidates all Arabic translations from DashboardTranslationSeeder and AccountingTranslationsSeeder
     */
    private function getPredefinedArabicTranslations(): array
    {
        return [
            // Navigation
            'navigation.dashboard' => 'لوحة التحكم',
            'navigation.users' => 'المستخدمون',
            'navigation.roles' => 'الأدوار',
            'navigation.permissions' => 'الصلاحيات',
            'navigation.maincore' => 'النواة الرئيسية',
            'navigation.system' => 'النظام',
            'navigation.my_profile' => 'ملفي الشخصي',
            'navigation.system_settings' => 'إعدادات النظام',
            'navigation.user' => 'المستخدمون',
            'navigation.role' => 'الأدوار',
            'navigation.permission' => 'الصلاحيات',
            'navigation.language' => 'اللغات',
            'navigation.currency' => 'العملات',
            'navigation.currencyrate' => 'أسعار الصرف',
            'navigation.setting' => 'الإعدادات',
            'navigation.theme' => 'السمات',
            'navigation.translation' => 'الترجمات',
            'navigation.paymentprovider' => 'مزودو الدفع',
            'navigation.paymentmethod' => 'طرق الدفع',
            'navigation.paymenttransaction' => 'معاملات الدفع',
            'navigation.shippingprovider' => 'مزودو الشحن',
            'navigation.shipment' => 'الشحنات',
            'navigation.notificationchannel' => 'قنوات الإشعارات',
            'navigation.notificationtemplate' => 'قوالب الإشعارات',
            'navigation.accounting' => 'المحاسبة',
            'navigation.journal_entries' => 'قيود اليومية',
            'navigation.accounts' => 'دليل الحسابات',
            'navigation.journals' => 'اليوميات',
            'navigation.vouchers' => 'السندات',
            'navigation.fiscal_years' => 'السنوات المالية',
            'navigation.periods' => 'الفترات',
            'navigation.projects' => 'المشاريع',
            'navigation.reports' => 'التقارير',

            // Actions
            'actions.create' => 'إنشاء',
            'actions.edit' => 'تعديل',
            'actions.delete' => 'حذف',
            'actions.save' => 'حفظ',
            'actions.cancel' => 'إلغاء',
            'actions.search' => 'بحث',

            // Dashboard
            'dashboard.welcome' => 'مرحباً بك في لوحة التحكم',
            'dashboard.overview' => 'نظرة عامة',
            'dashboard.statistics' => 'الإحصائيات',

            // Labels
            'labels.name' => 'الاسم',
            'labels.email' => 'البريد الإلكتروني',
            'labels.status' => 'الحالة',
            'labels.active' => 'نشط',
            'labels.inactive' => 'غير نشط',
            'labels.created_at' => 'تاريخ الإنشاء',
            'labels.updated_at' => 'تاريخ التحديث',

            // Messages
            'messages.created' => 'تم إنشاء السجل بنجاح',
            'messages.updated' => 'تم تحديث السجل بنجاح',
            'messages.deleted' => 'تم حذف السجل بنجاح',

            // Accounting
            'accounting.entry_information' => 'معلومات القيد',
            'accounting.journal_entry_lines' => 'بنود القيد',
            'accounting.journal' => 'اليومية',
            'accounting.entry_number' => 'رقم القيد',
            'accounting.entry_date' => 'تاريخ القيد',
            'accounting.fiscal_year' => 'السنة المالية',
            'accounting.period' => 'الفترة',
            'accounting.reference' => 'المرجع',
            'accounting.description' => 'الوصف',
            'accounting.branch' => 'الفرع',
            'accounting.cost_center' => 'مركز التكلفة',
            'accounting.project' => 'المشروع',
            'accounting.status' => 'الحالة',
            'accounting.account' => 'الحساب',
            'accounting.debit' => 'مدين',
            'accounting.credit' => 'دائن',
            'accounting.currency' => 'العملة',
            'accounting.exchange_rate' => 'سعر الصرف',
            'accounting.amount_in_base' => 'المبلغ بالعملة الأساسية',
            'accounting.total_debit' => 'إجمالي المدين',
            'accounting.total_credit' => 'إجمالي الدائن',
            'accounting.difference' => 'الفرق',
            'accounting.add_row' => 'إضافة صف',
            'accounting.add_line' => 'إضافة بند',
            'accounting.add_rows' => 'إضافة :count صفوف',
            'accounting.delete_selected' => 'حذف المحدد',
            'accounting.duplicate' => 'نسخ',
            'accounting.entries_not_balanced' => 'القيد غير متوازن',
            'accounting.entries_balanced' => 'القيد متوازن',
            'accounting.posted' => 'مقيد',
            'accounting.created_by' => 'تم الإنشاء بواسطة',
            'accounting.all' => 'الكل',
            'accounting.posted_only' => 'المقيدة فقط',
            'accounting.unposted_only' => 'غير المقيدة فقط',
            'accounting.from_date' => 'من تاريخ',
            'accounting.to_date' => 'إلى تاريخ',
            'accounting.submit_for_approval' => 'إرسال للموافقة',
            'accounting.approve' => 'موافقة',
            'accounting.reject' => 'رفض',
            'accounting.post' => 'تسجيل',
            'accounting.print' => 'طباعة',
            'accounting.export_pdf' => 'تصدير PDF',
            'accounting.export_excel' => 'تصدير Excel',
            'accounting.notes' => 'ملاحظات',
            'accounting.rejection_reason' => 'سبب الرفض',
            'accounting.cannot_submit' => 'لا يمكن إرسال القيد في الحالة الحالية.',
            'accounting.cannot_approve' => 'لا يمكن الموافقة على القيد في الحالة الحالية.',
            'accounting.cannot_reject' => 'لا يمكن رفض القيد في الحالة الحالية.',
            'accounting.cannot_post' => 'يجب الموافقة على القيد قبل التسجيل.',
            'accounting.already_posted' => 'القيد مسجل بالفعل.',
            'accounting.status.draft' => 'مسودة',
            'accounting.status.pending_approval' => 'في انتظار الموافقة',
            'accounting.status.approved' => 'موافق عليه',
            'accounting.status.rejected' => 'مرفوض',
            'accounting.status.posted' => 'مسجل',
            'accounting.validation.account_required' => 'الحساب مطلوب',
            'accounting.validation.debit_or_credit_required' => 'يجب إدخال إما مدين أو دائن، وليس كلاهما',
            'accounting.validation.amount_required' => 'مبلغ المدين أو الدائن مطلوب',
            'accounting.validation.exchange_rate_required' => 'سعر الصرف مطلوب للعملة الأجنبية',
            'accounting.validation.journal_required' => 'اليومية مطلوبة',
            'accounting.validation.entry_date_required' => 'تاريخ القيد مطلوب',
            'accounting.validation.branch_required' => 'الفرع مطلوب',
            'accounting.validation.lines_required' => 'يجب أن يكون هناك بند قيد واحد على الأقل',
            'accounting.validation.minimum_two_lines' => 'يجب أن يكون هناك بندان قيد على الأقل. يجب أن يتساوى إجمالي المدين والدائن.',
            'accounting.validation.line_error' => 'البند :index: :error',
            'accounting.validation.period_closed' => 'لا يمكن التسجيل في فترة مغلقة.',
            'accounting.validation.cannot_edit_posted' => 'لا يمكن تعديل قيد مسجل. قم بإنشاء قيد عكسي بدلاً من ذلك.',
            'accounting.validation.account_not_allowed' => 'الحساب غير نشط أو لا يسمح بالإدخال اليدوي.',
            'accounting.validation.debit_or_credit' => 'يجب أن يكون للبند إما مدين أو دائن، وليس كلاهما.',
            'accounting.validation.cannot_edit_status' => 'لا يمكن تعديل القيد في الحالة الحالية.',
            'accounting.validation.entries_not_balanced' => 'القيد غير متوازن. إجمالي المدين: :debits، إجمالي الدائن: :credits، الفرق: :difference',
            'accounting.validation.balance_details' => 'إجمالي المدين: :debits، إجمالي الدائن: :credits، الفرق: :difference',
            'accounting.auto_generated' => 'رقم القيد المولد تلقائياً',
            'accounting.external_reference' => 'رقم المرجع الخارجي (اختياري)',
            'accounting.name' => 'الاسم',
            'accounting.title' => 'المسمى الوظيفي',
            'accounting.date' => 'التاريخ',
            'accounting.type' => 'النوع',
            'accounting.debit_amount' => 'مبلغ المدين',
            'accounting.credit_amount' => 'مبلغ الدائن',
            'accounting.base_amount' => 'المبلغ الأساسي',
            'accounting.balanced' => 'متوازن',
            'accounting.summary' => 'الملخص',
            'accounting.total_debits' => 'إجمالي المدين',
            'accounting.total_credits' => 'إجمالي الدائن',
            'accounting.created_at' => 'تاريخ الإنشاء',
        ];
    }
}

