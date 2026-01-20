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
            'sidebar.finance.financetype' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],

            // Branch Transactions
            'navigation.finance_branch_transactions' => ['en' => 'Branch Transactions', 'ar' => 'معاملات الفروع'],
            'sidebar.finance.branch_transactions' => ['en' => 'Branch Transactions', 'ar' => 'معاملات الفروع'],

            // Reports
            'sidebar.finance.reports.branch_statement' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.reports.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'sidebar.finance.branchstatement' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.incomestatementbybranch' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
            'pages.finance.branch_statement.title' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'pages.finance.income_statement.title' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
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
            'forms.finance_types.remove_item' => ['en' => 'Remove item', 'ar' => 'إزالة العنصر'],
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
            'forms.branch_transactions.no_file_chosen' => ['en' => 'No file chosen', 'ar' => 'لم يتم اختيار ملف'],
            'forms.branch_transactions.select' => ['en' => 'Select', 'ar' => 'اختر'],
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
            'reports.branch_statement.closing_balance' => ['en' => 'Closing Balance', 'ar' => 'الرصيد الختامي'],
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
            'reports.income_statement.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'reports.income_statement.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'reports.income_statement.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.income_statement.filters.from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'reports.income_statement.filters.to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'reports.income_statement.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.income_statement.filters.kind' => ['en' => 'Kind (Optional)', 'ar' => 'النوع (اختياري)'],
            'reports.income_statement.filters.type' => ['en' => 'Type (Optional)', 'ar' => 'النوع (اختياري)'],
        ]);

        // ============================================
        // Reports - Income Report
        // ============================================
        $this->command->info('Step 8.1: Creating Income Report translations...');
        $translations = array_merge($translations, [
            'sidebar.finance.reports.income' => ['en' => 'Income Report', 'ar' => 'تقرير الإيرادات'],
            'pages.finance.income_report.title' => ['en' => 'Income Report', 'ar' => 'تقرير الإيرادات'],
            'reports.income.filters.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'reports.income.filters.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'reports.income.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.income.filters.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'reports.income.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.income.filters.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'reports.income.filters.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'reports.income.filters.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'reports.income.filters.search_placeholder' => ['en' => 'Search by payer, reference, notes...', 'ar' => 'بحث حسب الدافع، المرجع، الملاحظات...'],
            'reports.income.columns.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'reports.income.columns.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.income.columns.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'reports.income.columns.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.income.columns.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'reports.income.columns.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'reports.income.columns.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'reports.income.columns.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'reports.income.columns.payer' => ['en' => 'Payer', 'ar' => 'الدافع'],
            'reports.income.columns.attachment' => ['en' => 'Attachment', 'ar' => 'مرفق'],
            'reports.income.columns.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],
            'reports.income.summary.title' => ['en' => 'Summary', 'ar' => 'ملخص'],
            'reports.income.summary.total_income' => ['en' => 'Total Income', 'ar' => 'إجمالي الإيرادات'],
            'reports.income.summary.transaction_count' => ['en' => 'Transaction Count', 'ar' => 'عدد المعاملات'],
            'reports.income.summary.by_category' => ['en' => 'Summary by Category', 'ar' => 'ملخص حسب الفئة'],
            'reports.income.summary.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'reports.income.summary.count' => ['en' => 'Count', 'ar' => 'العدد'],
            'reports.income.summary.total_amount' => ['en' => 'Total Amount', 'ar' => 'إجمالي المبلغ'],
            'reports.income.detailed_transactions' => ['en' => 'Detailed Transactions', 'ar' => 'المعاملات التفصيلية'],
        ]);

        // ============================================
        // Reports - Expense Report
        // ============================================
        $this->command->info('Step 8.2: Creating Expense Report translations...');
        $translations = array_merge($translations, [
            'sidebar.finance.reports.expense' => ['en' => 'Expense Report', 'ar' => 'تقرير المصروفات'],
            'pages.finance.expense_report.title' => ['en' => 'Expense Report', 'ar' => 'تقرير المصروفات'],
            'reports.expense.filters.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'reports.expense.filters.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'reports.expense.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.expense.filters.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'reports.expense.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.expense.filters.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'reports.expense.filters.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'reports.expense.filters.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'reports.expense.filters.search_placeholder' => ['en' => 'Search by receiver, reference, notes...', 'ar' => 'بحث حسب المستلم، المرجع، الملاحظات...'],
            'reports.expense.columns.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'reports.expense.columns.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.expense.columns.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'reports.expense.columns.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.expense.columns.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'reports.expense.columns.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'reports.expense.columns.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'reports.expense.columns.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'reports.expense.columns.receiver' => ['en' => 'Receiver', 'ar' => 'المستلم'],
            'reports.expense.columns.attachment' => ['en' => 'Attachment', 'ar' => 'مرفق'],
            'reports.expense.columns.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],
            'reports.expense.summary.title' => ['en' => 'Summary', 'ar' => 'ملخص'],
            'reports.expense.summary.total_expenses' => ['en' => 'Total Expenses', 'ar' => 'إجمالي المصروفات'],
            'reports.expense.summary.transaction_count' => ['en' => 'Transaction Count', 'ar' => 'عدد المعاملات'],
            'reports.expense.summary.by_category' => ['en' => 'Summary by Category', 'ar' => 'ملخص حسب الفئة'],
            'reports.expense.summary.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'reports.expense.summary.count' => ['en' => 'Count', 'ar' => 'العدد'],
            'reports.expense.summary.total_amount' => ['en' => 'Total Amount', 'ar' => 'إجمالي المبلغ'],
            'reports.expense.detailed_transactions' => ['en' => 'Detailed Transactions', 'ar' => 'المعاملات التفصيلية'],
        ]);

        // ============================================
        // Import Page
        // ============================================
        $this->command->info('Step 8.3: Creating Import page translations...');
        $translations = array_merge($translations, [
            'sidebar.finance.import' => ['en' => 'Import from Excel', 'ar' => 'استيراد من Excel'],
            'pages.finance.import.title' => ['en' => 'Import from Excel', 'ar' => 'استيراد من Excel'],
            'pages.finance.import.download_template' => ['en' => 'Download Excel Template', 'ar' => 'تحميل قالب Excel'],
            'pages.finance.import.form_section' => ['en' => 'Import Settings', 'ar' => 'إعدادات الاستيراد'],
            'pages.finance.import.default_transaction_date' => ['en' => 'Default Transaction Date', 'ar' => 'تاريخ العملية الافتراضي'],
            'pages.finance.import.global_notes' => ['en' => 'Global Notes', 'ar' => 'ملاحظات عامة'],
            'pages.finance.import.allow_partial' => ['en' => 'Allow Partial Import', 'ar' => 'السماح بالاستيراد الجزئي'],
            'pages.finance.import.allow_partial_helper' => ['en' => 'Import valid rows even if some rows have errors', 'ar' => 'استيراد الصفوف الصحيحة حتى لو كان بعض الصفوف بها أخطاء'],
            'pages.finance.import.on_duplicate' => ['en' => 'On Duplicate', 'ar' => 'عند التكرار'],
            'pages.finance.import.duplicate_skip' => ['en' => 'Skip', 'ar' => 'تخطي'],
            'pages.finance.import.duplicate_update' => ['en' => 'Update', 'ar' => 'تحديث'],
            'pages.finance.import.excel_file' => ['en' => 'Excel File', 'ar' => 'ملف Excel'],
            'pages.finance.import.excel_file_helper' => ['en' => 'Upload .xlsx or .xls file', 'ar' => 'قم بتحميل ملف .xlsx أو .xls'],
            'pages.finance.import.submit' => ['en' => 'Import', 'ar' => 'استيراد'],
            'pages.finance.import.select_branch_first' => ['en' => 'Please select Branch first', 'ar' => 'الرجاء اختيار الفرع أولاً'],
            'pages.finance.import.select_kind_first' => ['en' => 'Please select Kind first', 'ar' => 'الرجاء اختيار النوع أولاً'],
            'pages.finance.import.select_type_first' => ['en' => 'Please select Finance Type first', 'ar' => 'الرجاء اختيار نوع المالية أولاً'],
            'pages.finance.import.no_file' => ['en' => 'Please upload an Excel file', 'ar' => 'الرجاء تحميل ملف Excel'],
            'pages.finance.import.invalid_currency' => ['en' => 'Invalid currency code', 'ar' => 'رمز العملة غير صحيح'],
            'pages.finance.import.import_failed' => ['en' => 'Import Failed', 'ar' => 'فشل الاستيراد'],
            'pages.finance.import.errors_found' => ['en' => 'Found :count errors.', 'ar' => 'تم العثور على :count أخطاء.'],
            'pages.finance.import.download_errors' => ['en' => 'Download Error Report', 'ar' => 'تحميل تقرير الأخطاء'],
            'pages.finance.import.partial_success' => ['en' => 'Partial Import Success', 'ar' => 'نجح الاستيراد جزئياً'],
            'pages.finance.import.import_success' => ['en' => 'Import Successful', 'ar' => 'نجح الاستيراد'],
            'pages.finance.import.success' => ['en' => 'Imported: :imported, Updated: :updated, Skipped: :skipped, Failed: :failed', 'ar' => 'تم الاستيراد: :imported، تم التحديث: :updated، تم التخطي: :skipped، فشل: :failed'],
            'pages.finance.import.downloading_errors' => ['en' => 'Downloading error report...', 'ar' => 'جاري تحميل تقرير الأخطاء...'],
            'pages.finance.import.error_report.title' => ['en' => 'Import Errors', 'ar' => 'أخطاء الاستيراد'],
            'pages.finance.import.error_report.row_number' => ['en' => 'Row Number', 'ar' => 'رقم الصف'],
            'pages.finance.import.error_report.reference_no' => ['en' => 'Reference No', 'ar' => 'رقم المرجع'],
            'pages.finance.import.error_report.error_message' => ['en' => 'Error Message', 'ar' => 'رسالة الخطأ'],
            'pages.finance.import.error_report.raw_values' => ['en' => 'Raw Values', 'ar' => 'القيم الأصلية'],
            'forms.common.select_placeholder' => ['en' => 'Select', 'ar' => 'اختر'],
            'forms.common.remove_item' => ['en' => 'Remove item', 'ar' => 'إزالة العنصر'],
            'forms.common.no_file_chosen' => ['en' => 'No file chosen', 'ar' => 'لم يتم اختيار ملف'],
        ]);

        // ============================================
        // Common Actions
        // ============================================
        $this->command->info('Step 9: Creating common action translations...');
        $translations = array_merge($translations, [
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'actions.create' => ['en' => 'Create', 'ar' => 'إنشاء'],
            'actions.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'actions.filter' => ['en' => 'Filter', 'ar' => 'تصفية'],
            'actions.toggle_columns' => ['en' => 'Toggle Columns', 'ar' => 'تبديل الأعمدة'],
            'actions.select_all' => ['en' => 'Select/Deselect all items for bulk actions', 'ar' => 'تحديد/إلغاء تحديد كافة العناصر للإجراءات الجماعية'],
            'actions.select_item' => ['en' => 'Select/Deselect item {number} for bulk actions', 'ar' => 'تحديد/إلغاء تحديد العنصر {number} للإجراءات الجماعية'],
        ]);

        // ============================================
        // Common Labels
        // ============================================
        $this->command->info('Step 10: Creating common label translations...');
        $translations = array_merge($translations, [
            'common.list' => ['en' => 'List', 'ar' => 'القائمة'],
            'common.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'common.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'common.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'common.sort' => ['en' => 'Sort', 'ar' => 'الترتيب'],
            'common.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'common.transactions' => ['en' => 'Transactions', 'ar' => 'المعاملات'],
            'common.all' => ['en' => 'All', 'ar' => 'الكل'],
            'common.active_only' => ['en' => 'Active only', 'ar' => 'نشط فقط'],
            'common.inactive_only' => ['en' => 'Inactive only', 'ar' => 'غير نشط فقط'],
            'reports.filters.title' => ['en' => 'Filters', 'ar' => 'الفلاتر'],
            'fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'fields.status_pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'fields.status_approved' => ['en' => 'Approved', 'ar' => 'معتمد'],
            'fields.status_rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'forms.branch_transactions.approval_notes' => ['en' => 'Approval Notes', 'ar' => 'ملاحظات الموافقة'],
            'forms.branch_transactions.rejection_reason' => ['en' => 'Rejection Reason', 'ar' => 'سبب الرفض'],
            'tables.branch_transactions.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.branch_transactions.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'notifications.approved' => ['en' => 'Transaction approved', 'ar' => 'تم اعتماد المعاملة'],
            'notifications.rejected' => ['en' => 'Transaction rejected', 'ar' => 'تم رفض المعاملة'],
            'actions.print_pdf' => ['en' => 'Print PDF', 'ar' => 'طباعة PDF'],
        ]);

        // ============================================
        // Save translations to database
        // ============================================
        $this->command->info('Step 11: Saving translations to database...');
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
