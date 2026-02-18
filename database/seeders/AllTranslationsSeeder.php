<?php

namespace Database\Seeders;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class AllTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping translations.');
            return;
        }

        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating all translations...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $translations = [];

        // ============================================
        // MAIN CORE - Navigation
        // ============================================
        $this->command->info('Step 1: MainCore - Navigation translations...');
        $translations = array_merge($translations, [
            'navigation.warehouse' => ['en' => 'Warehouse', 'ar' => 'المستودع'],
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
            'navigation.catalog' => ['en' => 'Catalog', 'ar' => 'دليل المنتجات'],
            'navigation.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'navigation.categories' => ['en' => 'Categories', 'ar' => 'الفئات'],
            'navigation.brand' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
            'navigation.brands' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
            'navigation.product' => ['en' => 'Product', 'ar' => 'المنتج'],
            'navigation.products' => ['en' => 'Products', 'ar' => 'المنتجات'],
            'navigation.batch' => ['en' => 'Batch', 'ar' => 'الدفعة'],
            'navigation.batches' => ['en' => 'Batches', 'ar' => 'الدفعات'],
            'navigation.sales' => ['en' => 'Sales', 'ar' => 'المبيعات'],
            'navigation.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'navigation.customers' => ['en' => 'Customers', 'ar' => 'العملاء'],
            'navigation.order' => ['en' => 'Order', 'ar' => 'الطلب'],
            'navigation.orders' => ['en' => 'Orders', 'ar' => 'الطلبات'],
            'navigation.invoice' => ['en' => 'Invoice', 'ar' => 'الفاتورة'],
            'navigation.invoices' => ['en' => 'Invoices', 'ar' => 'الفواتير'],
            'navigation.installment' => ['en' => 'Installment', 'ar' => 'القسط'],
            'navigation.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],
        ]);

        // ============================================
        // MAIN CORE - Sidebar
        // ============================================
        $this->command->info('Step 2: MainCore - Sidebar translations...');
        $translations = array_merge($translations, [
            'sidebar.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'sidebar.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'sidebar.sales' => ['en' => 'Sales', 'ar' => 'المبيعات'],
            'sidebar.products_inventory' => ['en' => 'Products & Inventory', 'ar' => 'المنتجات والمخزون'],
            'sidebar.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'sidebar.system' => ['en' => 'System', 'ar' => 'النظام'],
            'sidebar.notifications' => ['en' => 'Notifications', 'ar' => 'الإشعارات'],
            'sidebar.integrations' => ['en' => 'Integrations', 'ar' => 'التكاملات'],
            'sidebar.maincore' => ['en' => 'MainCore', 'ar' => 'النواة الرئيسية'],
            'sidebar.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'sidebar.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'sidebar.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'sidebar.recruitment' => ['en' => 'Recruitment', 'ar' => 'التوظيف'],
            'sidebar.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.rental' => ['en' => 'Rental', 'ar' => 'قسم التأجير'],
            'sidebar.general_settings' => ['en' => 'General Settings', 'ar' => 'الإعدادات العامة'],
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
            'sidebar.finance.types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],
            'sidebar.finance.branch_transactions' => ['en' => 'Branch Transactions', 'ar' => 'معاملات الفروع'],
            'sidebar.finance.reports.branch_statement' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.reports.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'sidebar.finance.reports.income' => ['en' => 'Income Report', 'ar' => 'تقرير الإيرادات'],
            'sidebar.finance.reports.expense' => ['en' => 'Expense Report', 'ar' => 'تقرير المصروفات'],
            'sidebar.finance.import' => ['en' => 'Import from Excel', 'ar' => 'استيراد من Excel'],
        ]);

        // ============================================
        // MAIN CORE - Menu
        // ============================================
        $this->command->info('Step 3: MainCore - Menu translations...');
        $translations = array_merge($translations, [
            'menu.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'menu.sales' => ['en' => 'Sales', 'ar' => 'المبيعات'],
            'menu.sales.customers' => ['en' => 'Customers', 'ar' => 'العملاء'],
            'menu.sales.orders' => ['en' => 'Orders', 'ar' => 'الطلبات'],
            'menu.sales.invoices' => ['en' => 'Invoices', 'ar' => 'الفواتير'],
            'menu.sales.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],
            'menu.products' => ['en' => 'Products & Inventory', 'ar' => 'المنتجات والمخزون'],
            'menu.products.categories' => ['en' => 'Categories', 'ar' => 'الفئات'],
            'menu.products.brands' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
            'menu.products.products' => ['en' => 'Products', 'ar' => 'المنتجات'],
            'menu.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'menu.accounting.accounts_tree' => ['en' => 'Accounts Tree', 'ar' => 'شجرة الحسابات'],
            'menu.accounting.journal_entries' => ['en' => 'Journal Entries', 'ar' => 'قيود اليومية'],
            'menu.accounting.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'menu.accounting.voucher_signatures' => ['en' => 'Voucher Signatures', 'ar' => 'توقيعات السندات'],
            'menu.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'menu.reports.trial_balance' => ['en' => 'Trial Balance', 'ar' => 'ميزان المراجعة'],
            'menu.reports.general_ledger' => ['en' => 'General Ledger', 'ar' => 'دفتر الأستاذ العام'],
            'menu.reports.income_statement' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'menu.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'menu.settings.branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'menu.settings.currencies' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'menu.settings.languages' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'menu.settings.translations' => ['en' => 'Translations', 'ar' => 'الترجمات'],
            'menu.system' => ['en' => 'System', 'ar' => 'النظام'],
            'menu.system.users' => ['en' => 'Users', 'ar' => 'المستخدمون'],
            'menu.system.roles' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'menu.system.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],
        ]);

        // ============================================
        // MAIN CORE - Actions
        // ============================================
        $this->command->info('Step 4: MainCore - Actions translations...');
        $translations = array_merge($translations, [
            'actions.create' => ['en' => 'Create', 'ar' => 'إنشاء'],
            'actions.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'actions.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'actions.update' => ['en' => 'Update', 'ar' => 'تحديث'],
            'actions.submit' => ['en' => 'Submit', 'ar' => 'إرسال'],
            'actions.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'actions.filter' => ['en' => 'Filter', 'ar' => 'تصفية'],
            'actions.view' => ['en' => 'View', 'ar' => 'عرض'],
            'actions.export' => ['en' => 'Export', 'ar' => 'تصدير'],
            'actions.import' => ['en' => 'Import', 'ar' => 'استيراد'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.approve' => ['en' => 'Approve', 'ar' => 'موافقة'],
            'actions.reject' => ['en' => 'Reject', 'ar' => 'رفض'],
            'actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'actions.close' => ['en' => 'Close', 'ar' => 'إغلاق'],
            'actions.duplicate' => ['en' => 'Duplicate', 'ar' => 'نسخ'],
        ]);

        // ============================================
        // MAIN CORE - Forms
        // ============================================
        $this->command->info('Step 5: MainCore - Forms translations...');
        $translations = array_merge($translations, [
            'forms.common.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.common.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.common.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'forms.common.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.common.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'forms.common.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'forms.common.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'forms.placeholders.name' => ['en' => 'Enter name', 'ar' => 'أدخل الاسم'],
            'forms.placeholders.code' => ['en' => 'Enter code', 'ar' => 'أدخل الرمز'],
            'forms.placeholders.select' => ['en' => 'Select...', 'ar' => 'اختر...'],
            'forms.placeholders.date' => ['en' => 'Select date', 'ar' => 'اختر التاريخ'],
            'forms.sections.basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'forms.sections.additional_info' => ['en' => 'Additional Information', 'ar' => 'معلومات إضافية'],
            'forms.sections.contact_info' => ['en' => 'Contact Information', 'ar' => 'معلومات الاتصال'],
        ]);

        // ============================================
        // MAIN CORE - Tables
        // ============================================
        $this->command->info('Step 6: MainCore - Tables translations...');
        $translations = array_merge($translations, [
            'tables.common.id' => ['en' => 'ID', 'ar' => 'المعرف'],
            'tables.common.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.common.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.common.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.common.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.common.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.common.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.common.actions' => ['en' => 'Actions', 'ar' => 'الإجراءات'],
            'tables.filters.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'tables.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.filters.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'tables.filters.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'tables.empty_state.heading' => ['en' => 'No records found', 'ar' => 'لا توجد سجلات'],
            'tables.empty_state.description' => ['en' => 'Get started by creating a new record', 'ar' => 'ابدأ بإنشاء سجل جديد'],
        ]);

        // ============================================
        // MAIN CORE - Voucher & Journal
        // ============================================
        $this->command->info('Step 7: MainCore - Voucher & Journal translations...');
        $translations = array_merge($translations, [
            'forms.journals.code' => ['en' => 'Journal Code', 'ar' => 'رمز اليومية'],
            'forms.journals.name' => ['en' => 'Journal Name', 'ar' => 'اسم اليومية'],
            'forms.journals.type' => ['en' => 'Journal Type', 'ar' => 'نوع اليومية'],
            'journals.types.general' => ['en' => 'General Journal', 'ar' => 'يومية عامة'],
            'journals.types.bank' => ['en' => 'Bank Journal', 'ar' => 'يومية بنكية'],
            'journals.types.cash' => ['en' => 'Cash Journal', 'ar' => 'يومية نقدية'],
            'tables.journals.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.journals.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.journals.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'vouchers.signatures.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'vouchers.signatures.title' => ['en' => 'Title', 'ar' => 'المسمى الوظيفي'],
            'vouchers.signatures.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'vouchers.signatures.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'vouchers.signatures.type_both' => ['en' => 'Both (Payment & Receipt)', 'ar' => 'كلاهما (صرف وقبض)'],
            'vouchers.signatures.type_receipt' => ['en' => 'Receipt Only', 'ar' => 'قبض فقط'],
            'vouchers.signatures.type_payment' => ['en' => 'Payment Only', 'ar' => 'صرف فقط'],
            'vouchers.actions.print_voucher' => ['en' => 'Print Voucher', 'ar' => 'طباعة السند'],
            'vouchers.actions.export_pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],
        ]);

        // ============================================
        // HR - Base
        // ============================================
        $this->command->info('Step 8: HR - Base translations...');
        $translations = array_merge($translations, [
            'navigation.groups.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'sidebar.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'navigation.hr_departments' => ['en' => 'Departments', 'ar' => 'الإدارات'],
            'navigation.hr_positions' => ['en' => 'Positions', 'ar' => 'المسميات الوظيفية'],
            'navigation.hr_blood_types' => ['en' => 'Blood Types', 'ar' => 'فصائل الدم'],
            'navigation.hr_identity_types' => ['en' => 'Identity Types', 'ar' => 'نوع الهوية'],
            'navigation.hr_banks' => ['en' => 'Banks', 'ar' => 'البنوك'],
            'navigation.hr_employees' => ['en' => 'Employees', 'ar' => 'الموظفين'],
            'navigation.hr_holidays' => ['en' => 'Official Holidays', 'ar' => 'العطلات الرسمية'],
            'fields.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'fields.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'fields.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'fields.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'fields.first_name' => ['en' => 'First Name', 'ar' => 'الاسم الأول'],
            'fields.last_name' => ['en' => 'Last Name', 'ar' => 'اسم العائلة'],
            'fields.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'fields.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'fields.gender' => ['en' => 'Gender', 'ar' => 'الجنس'],
            'fields.birth_date' => ['en' => 'Birth Date', 'ar' => 'تاريخ الميلاد'],
            'fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'fields.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'fields.position' => ['en' => 'Position', 'ar' => 'المنصب'],
            'gender.male' => ['en' => 'Male', 'ar' => 'ذكر'],
            'gender.female' => ['en' => 'Female', 'ar' => 'أنثى'],
            'forms.hr_departments.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_departments.active.label' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_departments.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_departments.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_employees.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_employees.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_employees.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.hr_employees.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_employees.position' => ['en' => 'Position', 'ar' => 'المنصب'],
            'tables.hr_employees.status' => ['en' => 'Status', 'ar' => 'الحالة'],
        ]);

        // ============================================
        // HR - Attendance
        // ============================================
        $this->command->info('Step 9: HR - Attendance translations...');
        $translations = array_merge($translations, [
            'navigation.groups.hr_attendance' => ['en' => 'HR > Attendance', 'ar' => 'الموارد البشرية > الحضور'],
            'sidebar.hr_attendance' => ['en' => 'Attendance', 'ar' => 'الحضور'],
            'navigation.hr_work_places' => ['en' => 'Work Places', 'ar' => 'أماكن العمل'],
            'navigation.hr_employee_groups' => ['en' => 'Employee Groups', 'ar' => 'مجموعات الموظفين'],
            'navigation.hr_work_schedules' => ['en' => 'Work Schedules', 'ar' => 'مواعيد العمل'],
            'navigation.hr_excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
            'navigation.hr_devices' => ['en' => 'Fingerprint Devices', 'ar' => 'أجهزة البصمة'],
            'navigation.hr_daily_attendance' => ['en' => 'Daily Attendance', 'ar' => 'الحضور اليومي'],
            'navigation.hr_monthly_report' => ['en' => 'Monthly Attendance Report', 'ar' => 'تقرير الحضور الشهري'],
            'fields.work_place' => ['en' => 'Work Place', 'ar' => 'مكان العمل'],
            'fields.start_time' => ['en' => 'Start Time', 'ar' => 'وقت البداية'],
            'fields.end_time' => ['en' => 'End Time', 'ar' => 'وقت النهاية'],
            'fields.present' => ['en' => 'Present', 'ar' => 'حاضر'],
            'fields.absent' => ['en' => 'Absent', 'ar' => 'غائب'],
            'fields.late_minutes' => ['en' => 'Late Minutes', 'ar' => 'دقائق التأخير'],
            'fields.overtime_minutes' => ['en' => 'Overtime Minutes', 'ar' => 'دقائق الإضافي'],
        ]);

        // ============================================
        // HR - Leaves
        // ============================================
        $this->command->info('Step 10: HR - Leaves translations...');
        $translations = array_merge($translations, [
            'navigation.hr_leaves' => ['en' => 'Leaves', 'ar' => 'الإجازات'],
            'navigation.hr_leave_types' => ['en' => 'Leave Types', 'ar' => 'أنواع الإجازات'],
            'navigation.hr_leave_requests' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'navigation.hr_leave_balance' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'fields.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البداية'],
            'fields.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ النهاية'],
            'fields.total_days' => ['en' => 'Total Days', 'ar' => 'إجمالي الأيام'],
            'fields.reason' => ['en' => 'Reason', 'ar' => 'السبب'],
            'status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'status.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'tables.hr_leave_requests.employee_name' => ['en' => 'Employee Name', 'ar' => 'اسم الموظف'],
            'tables.hr_leave_requests.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'tables.hr_leave_requests.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البداية'],
            'tables.hr_leave_requests.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ النهاية'],
            'tables.hr_leave_requests.total_days' => ['en' => 'Total Days', 'ar' => 'إجمالي الأيام'],
            'tables.hr_leave_requests.status' => ['en' => 'Status', 'ar' => 'الحالة'],
        ]);

        // ============================================
        // HR - Loans
        // ============================================
        $this->command->info('Step 11: HR - Loans translations...');
        $translations = array_merge($translations, [
            'navigation.hr_loans' => ['en' => 'Loans', 'ar' => 'القروض'],
            'navigation.hr_loan_types' => ['en' => 'Loan Types', 'ar' => 'أنواع القروض'],
            'fields.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'fields.loan_type' => ['en' => 'Loan Type', 'ar' => 'نوع القرض'],
            'fields.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'fields.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],
            'fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البدء'],
            'fields.due_date' => ['en' => 'Due Date', 'ar' => 'تاريخ الاستحقاق'],
            'status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'status.closed' => ['en' => 'Closed', 'ar' => 'مغلق'],
            'status.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
        ]);

        // ============================================
        // HR - Payroll
        // ============================================
        $this->command->info('Step 12: HR - Payroll translations...');
        $translations = array_merge($translations, [
            'sidebar.hr.payroll' => ['en' => 'Payroll', 'ar' => 'الرواتب'],
            'sidebar.hr.salary_components' => ['en' => 'Salary Components', 'ar' => 'المكونات المالية'],
            'sidebar.hr.employee_financial_profiles' => ['en' => 'Employee Financial Profiles', 'ar' => 'البيانات المالية للموظفين'],
            'tables.salary_components.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.salary_components.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.salary_components.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.hr_payroll.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_payroll.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_payroll.basic_salary' => ['en' => 'Basic Salary', 'ar' => 'الراتب الأساسي'],
            'tables.hr_payroll.total_earnings' => ['en' => 'Total Earnings', 'ar' => 'إجمالي المستحقات'],
            'tables.hr_payroll.total_deductions' => ['en' => 'Total Deductions', 'ar' => 'إجمالي الاستقطاعات'],
            'tables.hr_payroll.net_salary' => ['en' => 'Net Salary', 'ar' => 'صافي الراتب'],
            'forms.salary_components.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.salary_components.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.salary_components.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.salary_components.type.earning' => ['en' => 'Earning', 'ar' => 'مستحق'],
            'forms.salary_components.type.deduction' => ['en' => 'Deduction', 'ar' => 'استقطاع'],
        ]);

        // ============================================
        // FINANCE
        // ============================================
        $this->command->info('Step 13: Finance translations...');
        $translations = array_merge($translations, [
            'navigation.groups.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'navigation.finance_types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],
            'navigation.finance_branch_transactions' => ['en' => 'Branch Transactions', 'ar' => 'معاملات الفروع'],
            'forms.finance_types.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'forms.finance_types.kind_income' => ['en' => 'Income', 'ar' => 'إيراد'],
            'forms.finance_types.kind_expense' => ['en' => 'Expense', 'ar' => 'مصروف'],
            'forms.finance_types.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'forms.finance_types.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'forms.finance_types.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.finance_types.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'tables.finance_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.finance_types.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.branch_transactions.finance_type_id' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.branch_transactions.trx_date' => ['en' => 'Transaction Date', 'ar' => 'تاريخ العملية'],
            'forms.branch_transactions.branch_id' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'forms.branch_transactions.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'forms.branch_transactions.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'tables.branch_transactions.trx_date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.branch_transactions.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.branch_transactions.kind' => ['en' => 'Kind', 'ar' => 'النوع'],
            'tables.branch_transactions.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.branch_transactions.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'reports.branch_statement.title' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'reports.branch_statement.opening_balance' => ['en' => 'Opening Balance', 'ar' => 'الرصيد الافتتاحي'],
            'reports.branch_statement.total_income' => ['en' => 'Total Income', 'ar' => 'إجمالي الإيرادات'],
            'reports.branch_statement.total_expense' => ['en' => 'Total Expense', 'ar' => 'إجمالي المصروفات'],
            'reports.income_statement.title' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
            'reports.income.total_income' => ['en' => 'Total Income', 'ar' => 'إجمالي الإيرادات'],
            'reports.expense.total_expenses' => ['en' => 'Total Expenses', 'ar' => 'إجمالي المصروفات'],
        ]);

        // ============================================
        // ACCOUNTING
        // ============================================
        $this->command->info('Step 14: Accounting translations...');
        $translations = array_merge($translations, [
            'navigation.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'navigation.journal_entries' => ['en' => 'Journal Entries', 'ar' => 'قيود اليومية'],
            'navigation.accounts' => ['en' => 'Chart of Accounts', 'ar' => 'دليل الحسابات'],
            'navigation.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'accounting.entry_information' => ['en' => 'Entry Information', 'ar' => 'معلومات القيد'],
            'accounting.journal_entry_lines' => ['en' => 'Journal Entry Lines', 'ar' => 'بنود القيد'],
            'accounting.entry_number' => ['en' => 'Entry Number', 'ar' => 'رقم القيد'],
            'accounting.entry_date' => ['en' => 'Entry Date', 'ar' => 'تاريخ القيد'],
            'accounting.fiscal_year' => ['en' => 'Fiscal Year', 'ar' => 'السنة المالية'],
            'accounting.period' => ['en' => 'Period', 'ar' => 'الفترة'],
            'accounting.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'accounting.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'accounting.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'accounting.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'accounting.debit' => ['en' => 'Debit', 'ar' => 'مدين'],
            'accounting.credit' => ['en' => 'Credit', 'ar' => 'دائن'],
            'accounting.total_debit' => ['en' => 'Total Debit', 'ar' => 'إجمالي المدين'],
            'accounting.total_credit' => ['en' => 'Total Credit', 'ar' => 'إجمالي الدائن'],
            'accounting.difference' => ['en' => 'Difference', 'ar' => 'الفرق'],
            'accounting.posted' => ['en' => 'Posted', 'ar' => 'مقيد'],
            'accounting.status.draft' => ['en' => 'Draft', 'ar' => 'مسودة'],
            'accounting.status.pending_approval' => ['en' => 'Pending Approval', 'ar' => 'في انتظار الموافقة'],
            'accounting.status.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'accounting.status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'accounting.status.posted' => ['en' => 'Posted', 'ar' => 'مسجل'],
        ]);

        // ============================================
        // CLIENTS
        // ============================================
        $this->command->info('Step 15: Clients translations...');
        $translations = array_merge($translations, [
            'general.clients.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'general.clients.add_client' => ['en' => 'Add Client', 'ar' => 'إضافة عميل'],
            'general.clients.basic_data' => ['en' => 'Basic Data', 'ar' => 'البيانات الأساسية'],
            'general.clients.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'general.clients.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'general.clients.client_code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'general.clients.national_id' => ['en' => 'National ID', 'ar' => 'الهوية الوطنية'],
            'general.clients.mobile' => ['en' => 'Mobile', 'ar' => 'الجوال'],
            'general.clients.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'general.clients.birth_date' => ['en' => 'Birth Date', 'ar' => 'تاريخ الميلاد'],
            'general.clients.marital_status' => ['en' => 'Marital Status', 'ar' => 'الحالة الاجتماعية'],
            'general.clients.classification' => ['en' => 'Classification', 'ar' => 'التصنيف'],
            'general.clients.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'general.clients.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
        ]);

        // ============================================
        // BIOMETRIC
        // ============================================
        $this->command->info('Step 16: Biometric translations...');
        $translations = array_merge($translations, [
            'navigation.biometric_attendances' => ['en' => 'Biometric Attendance Logs', 'ar' => 'سجلات الحضور الحيوية'],
            'sidebar.biometric_attendances' => ['en' => 'Biometric Attendance Logs', 'ar' => 'سجلات الحضور الحيوية'],
            'tables.biometric_attendances.attended_at' => ['en' => 'Attended At', 'ar' => 'وقت الحضور'],
            'tables.biometric_attendances.type' => ['en' => 'Type', 'ar' => 'النوع'],
        ]);

        // ============================================
        // SERVICE TRANSFER
        // ============================================
        $this->command->info('Step 17: Service Transfer translations...');
        $translations = array_merge($translations, [
            'sidebar.servicetransfer' => ['en' => 'Service Transfers', 'ar' => 'طلبات نقل الخدمات'],
            'general.service_transfer' => ['en' => 'Service Transfer', 'ar' => 'نقل الخدمات'],
            'general.service_transfers' => ['en' => 'Service Transfers', 'ar' => 'طلبات نقل الخدمات'],
            'general.request_no' => ['en' => 'Request Number', 'ar' => 'رقم الطلب'],
            'general.request_date' => ['en' => 'Request Date', 'ar' => 'تاريخ الطلب'],
            'general.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'general.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'general.worker' => ['en' => 'Worker', 'ar' => 'العاملة'],
            'general.package' => ['en' => 'Package', 'ar' => 'الباقة'],
            'general.total_amount' => ['en' => 'Total Amount', 'ar' => 'المبلغ الإجمالي'],
            'general.payment_status' => ['en' => 'Payment Status', 'ar' => 'حالة الدفع'],
            'general.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'general.unpaid' => ['en' => 'Unpaid', 'ar' => 'غير مدفوع'],
            'general.partial' => ['en' => 'Partial', 'ar' => 'جزئي'],
        ]);

        // ============================================
        // COMPLAINT
        // ============================================
        $this->command->info('Step 18: Complaint translations...');
        $translations = array_merge($translations, [
            'sidebar.complaints.complaints' => ['en' => 'Complaints', 'ar' => 'قسم الشكاوي'],
            'complaint.fields.complaint_no' => ['en' => 'Complaint No', 'ar' => 'رقم الشكوى'],
            'complaint.fields.contract_type' => ['en' => 'Contract Type', 'ar' => 'نوع العقد'],
            'complaint.fields.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'complaint.fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'complaint.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'complaint.fields.priority' => ['en' => 'Priority', 'ar' => 'الأولوية'],
            'complaint.status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'complaint.status.in_progress' => ['en' => 'In Progress', 'ar' => 'قيد المعالجة'],
            'complaint.status.resolved' => ['en' => 'Resolved', 'ar' => 'تم الحل'],
            'complaint.status.closed' => ['en' => 'Closed', 'ar' => 'مغلق'],
            'tables.complaints.complaint_no' => ['en' => 'Complaint No', 'ar' => 'رقم الشكوى'],
            'tables.complaints.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'tables.complaints.status' => ['en' => 'Status', 'ar' => 'الحالة'],
        ]);

        // ============================================
        // HOUSING
        // ============================================
        $this->command->info('Step 19: Housing translations...');
        $translations = array_merge($translations, [
            'sidebar.recruitment_housing' => ['en' => 'Recruitment Housing', 'ar' => 'إيواء الاستقدام'],
            'sidebar.rental_housing' => ['en' => 'Rental Housing', 'ar' => 'إيواء التأجير'],
            'sidebar.housing_management' => ['en' => 'Housing Management', 'ar' => 'إدارة الإيواء'],
            'housing.dashboard.heading' => ['en' => 'Housing Dashboard', 'ar' => 'لوحة تحكم الإيواء'],
            'housing.requests.order_no' => ['en' => 'Order No', 'ar' => 'رقم الطلب'],
            'housing.requests.client' => ['en' => 'Client', 'ar' => 'العميل'],
            'housing.requests.laborer' => ['en' => 'Laborer', 'ar' => 'العمالة'],
            'housing.requests.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'housing.requests.select_status' => ['en' => 'Select Status', 'ar' => 'اختر الحالة'],
            'housing.salary.create' => ['en' => 'Add Salary for Worker', 'ar' => 'إضافة راتب للعامل'],
            'housing.salary.basic_salary' => ['en' => 'Basic Salary', 'ar' => 'الراتب الأساسي'],
            'housing.leave.create' => ['en' => 'Add New Leave', 'ar' => 'إضافة إجازة جديدة'],
            'housing.accommodation.create' => ['en' => 'Add New Accommodation Entry', 'ar' => 'إضافة إدخال إيواء جديد'],
        ]);

        // ============================================
        // RENTAL
        // ============================================
        $this->command->info('Step 20: Rental translations...');
        $translations = array_merge($translations, [
            'sidebar.rental' => ['en' => 'Rental', 'ar' => 'قسم التأجير'],
            'navigation.rental' => ['en' => 'Rental Packages', 'ar' => 'باقات التأجير'],
            'navigation.rental_contracts' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'navigation.rental_requests' => ['en' => 'Rental Requests', 'ar' => 'طلبات التأجير'],
            'rental.contracts.title' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'rental.fields.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'rental.fields.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'rental.fields.worker' => ['en' => 'Worker', 'ar' => 'العامل/العاملة'],
            'rental.fields.package' => ['en' => 'Package', 'ar' => 'الباقة'],
            'rental.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'rental.fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البدء'],
            'rental.fields.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ الانتهاء'],
            'rental.status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'rental.status.completed' => ['en' => 'Completed', 'ar' => 'مكتمل'],
            'rental.status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغي'],
        ]);

        // ============================================
        // RECRUITMENT
        // ============================================
        $this->command->info('Step 21: Recruitment translations...');
        $translations = array_merge($translations, [
            'navigation.groups.recruitment' => ['en' => 'Recruitment', 'ar' => 'التوظيف'],
            'navigation.recruitment_agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'navigation.recruitment_nationalities' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'navigation.recruitment_professions' => ['en' => 'Professions', 'ar' => 'المهن'],
            'navigation.recruitment_laborers' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'recruitment.fields.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'recruitment.fields.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'recruitment.fields.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'recruitment.fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'recruitment.fields.nationality' => ['en' => 'Nationality', 'ar' => 'الجنسية'],
            'recruitment.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'recruitment.fields.agent' => ['en' => 'Agent', 'ar' => 'الوكيل'],
        ]);

        // ============================================
        // RECRUITMENT CONTRACTS
        // ============================================
        $this->command->info('Step 22: Recruitment Contracts translations...');
        $translations = array_merge($translations, [
            'navigation.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'recruitment_contract.fields.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'recruitment_contract.fields.client' => ['en' => 'Client', 'ar' => 'العميل'],
            'recruitment_contract.fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'recruitment_contract.fields.visa_type' => ['en' => 'Visa Type', 'ar' => 'نوع التأشيرة'],
            'recruitment_contract.fields.visa_no' => ['en' => 'Visa No', 'ar' => 'رقم التأشيرة'],
            'recruitment_contract.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'recruitment_contract.fields.gender' => ['en' => 'Gender', 'ar' => 'الجنس'],
            'recruitment_contract.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'recruitment_contract.fields.payment_status' => ['en' => 'Payment Status', 'ar' => 'حالة الدفع'],
            'recruitment_contract.status.new' => ['en' => 'New', 'ar' => 'جديد'],
            'recruitment_contract.status.visa_issued' => ['en' => 'Visa Issued', 'ar' => 'تم إصدار التأشيرة'],
            'recruitment_contract.status.arrived_in_saudi_arabia' => ['en' => 'Arrived in Saudi Arabia', 'ar' => 'وصل للمملكة العربية السعودية'],
            'recruitment_contract.payment_status.unpaid' => ['en' => 'Unpaid', 'ar' => 'غير مدفوع'],
            'recruitment_contract.payment_status.partial' => ['en' => 'Partial', 'ar' => 'جزئي'],
            'recruitment_contract.payment_status.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
        ]);

        // ============================================
        // RECEIVING RECRUITMENT
        // ============================================
        $this->command->info('Step 23: Receiving Recruitment translations...');
        $translations = array_merge($translations, [
            'recruitment.receiving_labor.title' => ['en' => 'Receiving Labor', 'ar' => 'استلام العمالة'],
            'recruitment.receiving_labor.table.client' => ['en' => 'Client', 'ar' => 'العميل'],
            'recruitment.receiving_labor.table.worker' => ['en' => 'Worker Name', 'ar' => 'اسم العامل'],
            'recruitment.receiving_labor.table.arrival_date' => ['en' => 'Arrival Date', 'ar' => 'تاريخ الوصول'],
            'recruitment.receiving_labor.status.received' => ['en' => 'Received', 'ar' => 'تم الاستلام'],
            'recruitment.receiving_labor.status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'sidebar.recruitment.receiving_labor' => ['en' => 'Receiving Labor', 'ar' => 'استلام العمالة'],
        ]);

        // ============================================
        // PACKAGES
        // ============================================
        $this->command->info('Step 24: Packages translations...');
        $translations = array_merge($translations, [
            'navigation.offers' => ['en' => 'Packages', 'ar' => 'باقات العروض'],
            'navigation.recruitment' => ['en' => 'Recruitment Packages', 'ar' => 'باقات الاستقدام'],
            'navigation.service_transfer' => ['en' => 'Service Transfer Packages', 'ar' => 'باقات نقل الخدمات'],
            'fields.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'fields.base_price' => ['en' => 'Base Price', 'ar' => 'السعر الأساسي'],
            'fields.external_costs' => ['en' => 'External Costs', 'ar' => 'التكاليف الخارجية'],
            'fields.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'types.recruitment' => ['en' => 'Recruitment', 'ar' => 'استقدام'],
            'types.rental' => ['en' => 'Rental', 'ar' => 'تأجير'],
            'types.service_transfer' => ['en' => 'Service Transfer', 'ar' => 'نقل الخدمات'],
        ]);

        // ============================================
        // REPORTS
        // ============================================
        $this->command->info('Step 25: Reports translations...');
        $translations = array_merge($translations, [
            'reports.navigation' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'reports.filters.title' => ['en' => 'Report Filters', 'ar' => 'مرشحات التقرير'],
            'reports.filters.from_date' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'reports.filters.to_date' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'reports.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.filters.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'reports.export.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'reports.export.pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],
            'reports.export.excel' => ['en' => 'Export Excel', 'ar' => 'تصدير Excel'],
            'reports.trial_balance.title' => ['en' => 'Trial Balance', 'ar' => 'ميزان المراجعة'],
            'reports.trial_balance.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'reports.trial_balance.account_name' => ['en' => 'Account Name', 'ar' => 'اسم الحساب'],
            'reports.trial_balance.debits' => ['en' => 'Debits', 'ar' => 'مدين'],
            'reports.trial_balance.credits' => ['en' => 'Credits', 'ar' => 'دائن'],
            'reports.general_ledger.title' => ['en' => 'General Ledger', 'ar' => 'دفتر الأستاذ العام'],
            'reports.income_statement.title' => ['en' => 'Income Statement', 'ar' => 'قائمة الدخل'],
            'reports.balance_sheet.title' => ['en' => 'Balance Sheet', 'ar' => 'الميزانية العمومية'],
            'reports.cash_flow.title' => ['en' => 'Cash Flow Statement', 'ar' => 'تقرير التدفقات النقدية'],
            'reports.summary.total_debit' => ['en' => 'Total Debit', 'ar' => 'إجمالي المدين'],
            'reports.summary.total_credit' => ['en' => 'Total Credit', 'ar' => 'إجمالي الدائن'],
            'reports.summary.net_balance' => ['en' => 'Net Balance', 'ar' => 'الرصيد الصافي'],
        ]);

        // ============================================
        // COMMON
        // ============================================
        $this->command->info('Step 26: Common translations...');
        $translations = array_merge($translations, [
            'common.all' => ['en' => 'All', 'ar' => 'الكل'],
            'common.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'common.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],
            'common.yes' => ['en' => 'Yes', 'ar' => 'نعم'],
            'common.no' => ['en' => 'No', 'ar' => 'لا'],
            'common.select' => ['en' => 'Select', 'ar' => 'اختر'],
            'common.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'common.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'common.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'common.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'common.view' => ['en' => 'View', 'ar' => 'عرض'],
            'common.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'common.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
        ]);

        // ============================================
        // Save all translations to database
        // ============================================
        $this->command->info('Step 27: Saving all translations to database...');
        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            // Determine group (default to 'dashboard', but check for menu group)
            $group = 'dashboard';
            if (str_starts_with($key, 'menu.')) {
                $group = 'menu';
            } elseif (str_starts_with($key, 'packages.')) {
                $group = 'packages';
            }

            // English translation
            if (isset($values['en'])) {
                $resultEn = Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => $group,
                        'language_id' => $english->id,
                    ],
                    [
                        'value' => $values['en'],
                    ]
                );
                if ($resultEn->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            // Arabic translation
            if (isset($values['ar'])) {
                $resultAr = Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => $group,
                        'language_id' => $arabic->id,
                    ],
                    [
                        'value' => $values['ar'],
                    ]
                );
                if ($resultAr->wasRecentlyCreated && !isset($values['en'])) {
                    $created++;
                } elseif (!$resultAr->wasRecentlyCreated && !isset($values['en'])) {
                    $updated++;
                }
            }
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("✓ All translations created: {$created} created, {$updated} updated");
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
