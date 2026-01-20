<?php

namespace Database\Seeders\Finance;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class FinanceTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all Finance module translations (Arabic and English).
     */
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating Finance module translations...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('⚠ English or Arabic language not found. Skipping Finance translations.');
            return;
        }

        // ============================================
        // Navigation & Sidebar
        // ============================================
        $this->command->info('Step 1: Creating navigation translations...');
        $translations = [
            // Navigation Group
            'navigation.groups.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'sidebar.finance' => ['en' => 'Finance', 'ar' => 'المالية'],

            // Finance Types
            'navigation.finance_types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],
            'sidebar.finance.types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],

            // Branch Transactions
            'navigation.finance_branch_transactions' => ['en' => 'Branch Transactions', 'ar' => 'معاملات الفروع'],
            'sidebar.finance.branch_transactions' => ['en' => 'Branch Transactions', 'ar' => 'معاملات الفروع'],

            // Reports
            'sidebar.finance.reports.branch_statement' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.reports.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
        ];

        // ============================================
        // Finance Types - Forms
        // ============================================
        $this->command->info('Step 2: Creating Finance Types form translations...');
        $translations = array_merge($translations, [
            'forms.finance_types.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'forms.finance_types.kind_income' => ['en' => 'Income', 'ar' => 'إيراد'],
            'forms.finance_types.kind_expense' => ['en' => 'Expense', 'ar' => 'مصروف'],
            'forms.finance_types.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'forms.finance_types.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'forms.finance_types.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.finance_types.sort' => ['en' => 'Sort Order', 'ar' => 'ترتيب العرض'],
            'forms.finance_types.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
        ]);

        // ============================================
        // Finance Types - Tables
        // ============================================
        $this->command->info('Step 3: Creating Finance Types table translations...');
        $translations = array_merge($translations, [
            'tables.finance_types.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'tables.finance_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.finance_types.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.finance_types.sort' => ['en' => 'Sort', 'ar' => 'الترتيب'],
            'tables.finance_types.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.finance_types.transactions_count' => ['en' => 'Transactions', 'ar' => 'المعاملات'],
        ]);

        // ============================================
        // Branch Transactions - Forms
        // ============================================
        $this->command->info('Step 4: Creating Branch Transactions form translations...');
        $translations = array_merge($translations, [
            'forms.branch_transactions.finance_type_id' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.branch_transactions.kind_filter' => ['en' => 'Filter by Kind', 'ar' => 'تصفية حسب النوع'],
            'forms.branch_transactions.trx_date' => ['en' => 'Transaction Date', 'ar' => 'تاريخ العملية'],
            'forms.branch_transactions.branch_id' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'forms.branch_transactions.country_id' => ['en' => 'Country', 'ar' => 'الدولة'],
            'forms.branch_transactions.currency_id' => ['en' => 'Currency', 'ar' => 'العملة'],
            'forms.branch_transactions.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'forms.branch_transactions.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'forms.branch_transactions.recipient_name' => ['en' => 'Recipient Name', 'ar' => 'اسم المستلم'],
            'forms.branch_transactions.reference_no' => ['en' => 'Reference No', 'ar' => 'رقم المرجع'],
            'forms.branch_transactions.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'forms.branch_transactions.attachment_path' => ['en' => 'Attachment', 'ar' => 'مرفق'],
        ]);

        // ============================================
        // Branch Transactions - Tables
        // ============================================
        $this->command->info('Step 5: Creating Branch Transactions table translations...');
        $translations = array_merge($translations, [
            'tables.branch_transactions.trx_date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.branch_transactions.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.branch_transactions.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'tables.branch_transactions.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.branch_transactions.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'tables.branch_transactions.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'tables.branch_transactions.reference_no' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'tables.branch_transactions.recipient_name' => ['en' => 'Recipient', 'ar' => 'المستلم'],
            'tables.branch_transactions.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'tables.branch_transactions.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'tables.branch_transactions.running_balance' => ['en' => 'Running Balance', 'ar' => 'الرصيد الجاري'],
        ]);

        // ============================================
        // Branch Transactions - Filters
        // ============================================
        $this->command->info('Step 6: Creating Branch Transactions filter translations...');
        $translations = array_merge($translations, [
            'tables.branch_transactions.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.branch_transactions.filters.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'tables.branch_transactions.filters.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.branch_transactions.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'tables.branch_transactions.filters.date_range' => ['en' => 'Date Range', 'ar' => 'نطاق التاريخ'],
        ]);

        // ============================================
        // Reports - Branch Statement
        // ============================================
        $this->command->info('Step 7: Creating Branch Statement report translations...');
        $translations = array_merge($translations, [
            'reports.branch_statement.title' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'reports.branch_statement.opening_balance' => ['en' => 'Opening Balance', 'ar' => 'الرصيد الافتتاحي'],
            'reports.branch_statement.total_income' => ['en' => 'Total Income', 'ar' => 'إجمالي الإيرادات'],
            'reports.branch_statement.total_expense' => ['en' => 'Total Expense', 'ar' => 'إجمالي المصروفات'],
            'reports.branch_statement.net_change' => ['en' => 'Net Change', 'ar' => 'صافي التغيير'],
            'reports.branch_statement.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.branch_statement.filters.from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'reports.branch_statement.filters.to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'reports.branch_statement.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.branch_statement.filters.kind' => ['en' => 'Kind (Optional)', 'ar' => 'النوع (اختياري)'],
            'reports.branch_statement.filters.type' => ['en' => 'Type (Optional)', 'ar' => 'النوع (اختياري)'],
        ]);

        // ============================================
        // Reports - Income Statement
        // ============================================
        $this->command->info('Step 8: Creating Income Statement report translations...');
        $translations = array_merge($translations, [
            'reports.income_statement.title' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
            'reports.income_statement.total_income' => ['en' => 'Total Income', 'ar' => 'إجمالي الإيرادات'],
            'reports.income_statement.total_expense' => ['en' => 'Total Expense', 'ar' => 'إجمالي المصروفات'],
            'reports.income_statement.net_profit' => ['en' => 'Net Profit', 'ar' => 'صافي الربح'],
            'reports.income_statement.income_section' => ['en' => 'INCOME', 'ar' => 'الإيرادات'],
            'reports.income_statement.expense_section' => ['en' => 'EXPENSE', 'ar' => 'المصروفات'],
            'reports.income_statement.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.income_statement.filters.from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'reports.income_statement.filters.to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'reports.income_statement.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
        ]);

        // ============================================
        // Common Actions
        // ============================================
        $this->command->info('Step 9: Creating common action translations...');
        $translations = array_merge($translations, [
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
        ]);

        // ============================================
        // Save translations to database
        // ============================================
        $this->command->info('Step 10: Saving translations to database...');
        $created = 0;

        foreach ($translations as $key => $values) {
            if (isset($values['en'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $english->id,
                    ],
                    ['value' => $values['en']]
                );
                $created++;
            }

            if (isset($values['ar'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $arabic->id,
                    ],
                    ['value' => $values['ar']]
                );
                $created++;
            }
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("✓ Finance translations created: {$created} entries");
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
