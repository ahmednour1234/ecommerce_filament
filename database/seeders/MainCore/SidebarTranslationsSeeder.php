<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class SidebarTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping sidebar translations.');
            return;
        }

        $translations = [
            // Navigation Groups
            'sidebar.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'sidebar.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'sidebar.sales' => ['en' => 'Sales', 'ar' => 'المبيعات'],
            'sidebar.products_inventory' => ['en' => 'Products & Inventory', 'ar' => 'المنتجات والمخزون'],
            'sidebar.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'sidebar.system' => ['en' => 'System', 'ar' => 'النظام'],
            'sidebar.notifications' => ['en' => 'Notifications', 'ar' => 'الإشعارات'],
            'sidebar.integrations' => ['en' => 'Integrations', 'ar' => 'التكاملات'],
            'sidebar.maincore' => ['en' => 'MainCore', 'ar' => 'النواة الرئيسية'],

            // Accounting Items
            'sidebar.accounting.accounts_tree' => ['en' => 'Accounts Tree', 'ar' => 'شجرة الحسابات'],
            'sidebar.accounting.journal_entries' => ['en' => 'Journal Entries', 'ar' => 'قيود اليومية'],
            'sidebar.accounting.journals' => ['en' => 'Journals', 'ar' => 'اليوميات'],
            'sidebar.accounting.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'sidebar.accounting.accounts' => ['en' => 'Accounts', 'ar' => 'الحسابات'],
            'sidebar.accounting.bank_accounts' => ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية'],
            'sidebar.accounting.assets' => ['en' => 'Assets', 'ar' => 'الأصول'],
            'sidebar.accounting.cost_centers' => ['en' => 'Cost Centers', 'ar' => 'مراكز التكلفة'],
            'sidebar.accounting.fiscal_years' => ['en' => 'Fiscal Years', 'ar' => 'السنوات المالية'],
            'sidebar.accounting.periods' => ['en' => 'Periods', 'ar' => 'الفترات'],
            'sidebar.accounting.projects' => ['en' => 'Projects', 'ar' => 'المشاريع'],
            'sidebar.accounting.trial_balance' => ['en' => 'Trial Balance', 'ar' => 'ميزان المراجعة'],
            'sidebar.accounting.sales_report' => ['en' => 'Sales Report', 'ar' => 'تقرير المبيعات'],
            'sidebar.accounting.orders_report' => ['en' => 'Orders Report', 'ar' => 'تقرير الطلبات'],
            'sidebar.accounting.invoices_report' => ['en' => 'Invoices Report', 'ar' => 'تقرير الفواتير'],

            // Reports Items
            'sidebar.reports.trial_balance' => ['en' => 'Trial Balance', 'ar' => 'ميزان المراجعة'],
            'sidebar.reports.general_ledger' => ['en' => 'General Ledger', 'ar' => 'دفتر الأستاذ العام'],
            'sidebar.reports.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'sidebar.reports.account_statement' => ['en' => 'Account Statement', 'ar' => 'كشف حساب'],
            'sidebar.reports.balance_sheet' => ['en' => 'Balance Sheet', 'ar' => 'الميزانية العمومية'],
            'sidebar.reports.cash_flow' => ['en' => 'Cash Flow', 'ar' => 'قائمة التدفقات النقدية'],
            'sidebar.reports.vat_report' => ['en' => 'VAT Report', 'ar' => 'تقرير ضريبة القيمة المضافة'],
            'sidebar.reports.changes_in_equity' => ['en' => 'Changes in Equity', 'ar' => 'تغيرات حقوق الملكية'],
            'sidebar.reports.fixed_assets' => ['en' => 'Fixed Assets Report', 'ar' => 'تقرير الأصول الثابتة'],
            'sidebar.reports.journal_entries_by_year' => ['en' => 'Journal Entries by Year', 'ar' => 'قيود اليومية حسب السنة'],
            'sidebar.reports.accounts_receivable' => ['en' => 'Accounts Receivable', 'ar' => 'الذمم المدينة'],
            'sidebar.reports.accounts_payable_aging_current' => ['en' => 'A/P Aging Current', 'ar' => 'أعمار الديون المدينة'],
            'sidebar.reports.accounts_payable_aging_overdue' => ['en' => 'A/P Aging Overdue', 'ar' => 'أعمار الديون الدائنة'],
            'sidebar.reports.financial_position' => ['en' => 'Financial Position', 'ar' => 'المركز المالي'],
            'sidebar.reports.financial_performance' => ['en' => 'Financial Performance', 'ar' => 'الأداء المالي'],
            'sidebar.reports.comparisons' => ['en' => 'Comparisons Report', 'ar' => 'تقرير المقارنات'],

            // Sales Items
            'sidebar.sales.customers' => ['en' => 'Customers', 'ar' => 'العملاء'],
            'sidebar.sales.orders' => ['en' => 'Orders', 'ar' => 'الطلبات'],
            'sidebar.sales.invoices' => ['en' => 'Invoices', 'ar' => 'الفواتير'],
            'sidebar.sales.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],

            // Products & Inventory Items
            'sidebar.products_inventory.categories' => ['en' => 'Categories', 'ar' => 'الفئات'],
            'sidebar.products_inventory.brands' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
            'sidebar.products_inventory.products' => ['en' => 'Products', 'ar' => 'المنتجات'],
            'sidebar.products_inventory.batches' => ['en' => 'Batches', 'ar' => 'الدفعات'],

            // Settings Items
            'sidebar.settings.branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'sidebar.settings.currencies' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'sidebar.settings.currency_rates' => ['en' => 'Currency Rates', 'ar' => 'أسعار العملات'],
            'sidebar.settings.languages' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'sidebar.settings.translations' => ['en' => 'Translations', 'ar' => 'الترجمات'],
            'sidebar.settings.themes' => ['en' => 'Themes', 'ar' => 'السمات'],
            'sidebar.settings.app_settings' => ['en' => 'App Settings', 'ar' => 'إعدادات التطبيق'],

            // System Items
            'sidebar.system.users' => ['en' => 'Users', 'ar' => 'المستخدمون'],
            'sidebar.system.roles' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'sidebar.system.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],

            // Notifications Items
            'sidebar.notifications.channels' => ['en' => 'Notification Channels', 'ar' => 'قنوات الإشعارات'],
            'sidebar.notifications.templates' => ['en' => 'Notification Templates', 'ar' => 'قوالب الإشعارات'],

            // Integrations Items
            'sidebar.integrations.payment_methods' => ['en' => 'Payment Methods', 'ar' => 'طرق الدفع'],
            'sidebar.integrations.payment_providers' => ['en' => 'Payment Providers', 'ar' => 'مزودو الدفع'],
            'sidebar.integrations.payment_transactions' => ['en' => 'Payment Transactions', 'ar' => 'معاملات الدفع'],
            'sidebar.integrations.shipments' => ['en' => 'Shipments', 'ar' => 'الشحنات'],
            'sidebar.integrations.shipping_providers' => ['en' => 'Shipping Providers', 'ar' => 'مزودو الشحن'],

            // MainCore Items
            'sidebar.maincore.warehouses' => ['en' => 'Warehouses', 'ar' => 'المستودعات'],
            'sidebar.maincore.main_settings' => ['en' => 'Main Settings', 'ar' => 'الإعدادات الرئيسية'],
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

        $this->command->info("Sidebar translations seeded: {$created} created, {$updated} updated.");
    }
}

