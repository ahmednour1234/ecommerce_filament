<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class NavigationTranslationsSeeder extends Seeder
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
            // Warehouse
            'navigation.warehouse' => ['en' => 'Warehouse', 'ar' => 'المستودع'],

            // Reports Group
            'navigation.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'navigation.trial_balance' => ['en' => 'Trial Balance', 'ar' => 'ميزان المراجعة'],
            'navigation.general_ledger' => ['en' => 'General Ledger', 'ar' => 'دفتر الأستاذ العام'],
            'navigation.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'navigation.account_statement' => ['en' => 'Account Statement', 'ar' => 'كشف حساب'],
            'navigation.balance_sheet' => ['en' => 'Balance Sheet', 'ar' => 'الميزانية العمومية'],
            'navigation.cash_flow' => ['en' => 'Cash Flow', 'ar' => 'قائمة التدفقات النقدية'],
            'navigation.vat_report' => ['en' => 'VAT Report', 'ar' => 'تقرير ضريبة القيمة المضافة'],
            'navigation.fixed_assets_report' => ['en' => 'Fixed Assets Report', 'ar' => 'تقرير الأصول الثابتة'],
            'navigation.journal_entries_by_year' => ['en' => 'Journal Entries by Year', 'ar' => 'قيود اليومية حسب السنة'],
            'navigation.accounts_receivable' => ['en' => 'A/R Customers', 'ar' => 'العملاء (ذمم مدينة)'],
            'navigation.accounts_payable_aging_current' => ['en' => 'A/P Aging Current', 'ar' => 'الموردون (ذمم دائنة) - الحالية'],
            'navigation.accounts_payable_aging_overdue' => ['en' => 'A/P Aging Overdue', 'ar' => 'الموردون (ذمم دائنة) - المتأخرة'],
            'navigation.financial_position' => ['en' => 'Financial Position', 'ar' => 'المركز المالي'],
            'navigation.changes_in_equity' => ['en' => 'Changes in Equity', 'ar' => 'تغيرات حقوق الملكية'],
            'navigation.financial_performance' => ['en' => 'Financial Performance', 'ar' => 'الأداء المالي'],
            'navigation.comparisons_report' => ['en' => 'Comparisons Report', 'ar' => 'تقرير المقارنات'],

            // Accounting Group (المحاسبة)
            'navigation.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'navigation.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'navigation.accounts' => ['en' => 'Accounts', 'ar' => 'الحسابات'],
            'navigation.journal' => ['en' => 'Journal', 'ar' => 'اليومية'],
            'navigation.journals' => ['en' => 'Journals', 'ar' => 'اليوميات'],
            'navigation.journal_entry' => ['en' => 'Journal Entry', 'ar' => 'قيد اليومية'],
            'navigation.journal_entries' => ['en' => 'Journal Entries', 'ar' => 'قيود اليومية'],
            'navigation.voucher' => ['en' => 'Voucher', 'ar' => 'السند'],
            'navigation.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'navigation.cost_center' => ['en' => 'Cost Center', 'ar' => 'مركز التكلفة'],
            'navigation.cost_centers' => ['en' => 'Cost Centers', 'ar' => 'مراكز التكلفة'],
            'navigation.asset' => ['en' => 'Asset', 'ar' => 'الأصل'],
            'navigation.assets' => ['en' => 'Assets', 'ar' => 'الأصول'],
            'navigation.fiscal_year' => ['en' => 'Fiscal Year', 'ar' => 'السنة المالية'],
            'navigation.fiscal_years' => ['en' => 'Fiscal Years', 'ar' => 'السنوات المالية'],
            'navigation.period' => ['en' => 'Period', 'ar' => 'الفترة'],
            'navigation.periods' => ['en' => 'Periods', 'ar' => 'الفترات'],
            'navigation.project' => ['en' => 'Project', 'ar' => 'المشروع'],
            'navigation.projects' => ['en' => 'Projects', 'ar' => 'المشاريع'],
            'navigation.bank_account' => ['en' => 'Bank Account', 'ar' => 'الحساب البنكي'],
            'navigation.bank_accounts' => ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية'],

            // Catalog Group
            'navigation.catalog' => ['en' => 'Catalog', 'ar' => 'دليل المنتجات'],
            'navigation.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'navigation.categories' => ['en' => 'Categories', 'ar' => 'الفئات'],
            'navigation.brand' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
            'navigation.brands' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
            'navigation.product' => ['en' => 'Product', 'ar' => 'المنتج'],
            'navigation.products' => ['en' => 'Products', 'ar' => 'المنتجات'],
            'navigation.batch' => ['en' => 'Batch', 'ar' => 'الدفعة'],
            'navigation.batches' => ['en' => 'Batches', 'ar' => 'الدفعات'],

            // Sales Group
            'navigation.sales' => ['en' => 'Sales', 'ar' => 'المبيعات'],
            'navigation.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'navigation.customers' => ['en' => 'Customers', 'ar' => 'العملاء'],
            'navigation.order' => ['en' => 'Order', 'ar' => 'الطلب'],
            'navigation.orders' => ['en' => 'Orders', 'ar' => 'الطلبات'],
            'navigation.invoice' => ['en' => 'Invoice', 'ar' => 'الفاتورة'],
            'navigation.invoices' => ['en' => 'Invoices', 'ar' => 'الفواتير'],
            'navigation.installment' => ['en' => 'Installment', 'ar' => 'القسط'],
            'navigation.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],

            // Accounting Pages (Old/Alternative)
            'navigation.accounting_pages' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'navigation.accounts_tree_page' => ['en' => 'Accounts Tree Page', 'ar' => 'صفحة شجرة الحسابات'],
            'navigation.trial_balance_page' => ['en' => 'Trial Balance Page', 'ar' => 'صفحة ميزان المراجعة'],
            'navigation.sales_report_page' => ['en' => 'Sales Report Page', 'ar' => 'صفحة تقرير المبيعات'],
            'navigation.orders_report_page' => ['en' => 'Orders Report Page', 'ar' => 'صفحة تقرير الطلبات'],
            'navigation.invoices_report_page' => ['en' => 'Invoices Report Page', 'ar' => 'صفحة تقرير الفواتير'],
        ];

        $this->command->info('Seeding navigation translations...');

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

        $this->command->info('✓ Navigation translations seeded successfully!');
        $this->command->info('Total translations: ' . count($translations) . ' keys × 2 languages = ' . (count($translations) * 2) . ' records');
    }
}

