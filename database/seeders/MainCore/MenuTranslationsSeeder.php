<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class MenuTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping menu translations.');
            return;
        }

        $translations = [
            // Dashboard
            'menu.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],

            // Sales Module
            'menu.sales' => ['en' => 'Sales', 'ar' => 'المبيعات'],
            'menu.sales.customers' => ['en' => 'Customers', 'ar' => 'العملاء'],
            'menu.sales.orders' => ['en' => 'Orders', 'ar' => 'الطلبات'],
            'menu.sales.invoices' => ['en' => 'Invoices', 'ar' => 'الفواتير'],
            'menu.sales.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],
            'menu.sales.returns' => ['en' => 'Returns', 'ar' => 'المرتجعات'],

            // Products & Inventory Module
            'menu.products' => ['en' => 'Products & Inventory', 'ar' => 'المنتجات والمخزون'],
            'menu.products_inventory' => ['en' => 'Products & Inventory', 'ar' => 'المنتجات والمخزون'],
            'menu.products.categories' => ['en' => 'Categories', 'ar' => 'الفئات'],
            'menu.products.brands' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
            'menu.products.products' => ['en' => 'Products', 'ar' => 'المنتجات'],
            'menu.products.batches' => ['en' => 'Batches', 'ar' => 'الدفعات'],
            'menu.products.stock_movements' => ['en' => 'Stock Movements', 'ar' => 'حركات المخزون'],

            // Accounting Module
            'menu.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'menu.accounting.accounts_tree' => ['en' => 'Accounts Tree', 'ar' => 'شجرة الحسابات'],
            'menu.accounting.journal_entries' => ['en' => 'Journal Entries', 'ar' => 'قيود اليومية'],
            'menu.accounting.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'menu.accounting.cost_centers' => ['en' => 'Cost Centers', 'ar' => 'مراكز التكلفة'],
            'menu.accounting.bank_accounts' => ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية'],
            'menu.accounting.fixed_assets' => ['en' => 'Fixed Assets', 'ar' => 'الأصول الثابتة'],
            'menu.accounting.fiscal_years' => ['en' => 'Fiscal Years', 'ar' => 'السنوات المالية'],
            'menu.accounting.periods' => ['en' => 'Periods', 'ar' => 'الفترات'],
            'menu.accounting.projects' => ['en' => 'Projects', 'ar' => 'المشاريع'],

            // Reports Module
            'menu.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'menu.reports.trial_balance' => ['en' => 'Trial Balance', 'ar' => 'ميزان المراجعة'],
            'menu.reports.general_ledger' => ['en' => 'General Ledger', 'ar' => 'دفتر الأستاذ العام'],
            'menu.reports.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'menu.reports.account_statement' => ['en' => 'Account Statement', 'ar' => 'كشف حساب'],
            'menu.reports.balance_sheet' => ['en' => 'Balance Sheet', 'ar' => 'الميزانية العمومية'],
            'menu.reports.cash_flow' => ['en' => 'Cash Flow', 'ar' => 'قائمة التدفقات النقدية'],
            'menu.reports.vat_report' => ['en' => 'VAT Report', 'ar' => 'تقرير ضريبة القيمة المضافة'],
            'menu.reports.changes_in_equity' => ['en' => 'Changes in Equity', 'ar' => 'تغيرات حقوق الملكية'],
            'menu.reports.comparisons_report' => ['en' => 'Comparisons Report', 'ar' => 'تقرير المقارنات'],
            'menu.reports.financial_performance' => ['en' => 'Financial Performance', 'ar' => 'الأداء المالي'],
            'menu.reports.accounts_receivable' => ['en' => 'Accounts Receivable', 'ar' => 'الذمم المدينة'],
            'menu.reports.accounts_payable_aging' => ['en' => 'Accounts Payable Aging', 'ar' => 'تقرير أعمار الدائنين'],
            'menu.reports.financial_position' => ['en' => 'Financial Position', 'ar' => 'المركز المالي'],
            'menu.reports.journal_entries_by_year' => ['en' => 'Journal Entries by Year', 'ar' => 'قيود اليومية حسب السنة'],
            'menu.reports.fixed_assets' => ['en' => 'Fixed Assets', 'ar' => 'الأصول الثابتة'],

            // Integrations Module
            'menu.integrations' => ['en' => 'Integrations', 'ar' => 'التكاملات'],
            'menu.integrations.payments' => ['en' => 'Payments', 'ar' => 'المدفوعات'],
            'menu.integrations.payments.methods' => ['en' => 'Payment Methods', 'ar' => 'طرق الدفع'],
            'menu.integrations.payments.providers' => ['en' => 'Payment Providers', 'ar' => 'مزودو الدفع'],
            'menu.integrations.payments.transactions' => ['en' => 'Payment Transactions', 'ar' => 'معاملات الدفع'],
            'menu.integrations.shipping' => ['en' => 'Shipping', 'ar' => 'الشحن'],
            'menu.integrations.shipping.shipments' => ['en' => 'Shipments', 'ar' => 'الشحنات'],
            'menu.integrations.shipping.providers' => ['en' => 'Shipping Providers', 'ar' => 'مزودو الشحن'],

            // Notifications Module
            'menu.notifications' => ['en' => 'Notifications', 'ar' => 'الإشعارات'],
            'menu.notifications.channels' => ['en' => 'Notification Channels', 'ar' => 'قنوات الإشعارات'],
            'menu.notifications.templates' => ['en' => 'Notification Templates', 'ar' => 'قوالب الإشعارات'],

            // Settings Module
            'menu.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'menu.settings.app_settings' => ['en' => 'App Settings', 'ar' => 'إعدادات التطبيق'],
            'menu.settings.branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'menu.settings.currencies' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'menu.settings.languages' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'menu.settings.translations' => ['en' => 'Translations', 'ar' => 'الترجمات'],

            // System Module
            'menu.system' => ['en' => 'System', 'ar' => 'النظام'],
            'menu.system.users' => ['en' => 'Users', 'ar' => 'المستخدمون'],
            'menu.system.roles' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'menu.system.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            // English translation
            Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'menu',
                    'language_id' => $english->id,
                ],
                [
                    'value' => $values['en'],
                ]
            );

            // Arabic translation
            $result = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'menu',
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );

            if ($result->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Menu translations seeded: {$created} created, {$updated} updated.");
    }
}

