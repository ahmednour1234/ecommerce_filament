<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class CompletePagesTranslationsSeeder extends Seeder
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
            // ============================================
            // ACCOUNTING RESOURCE TRANSLATIONS
            // ============================================

            // Account Resource
            'forms.accounts.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'forms.accounts.account_name' => ['en' => 'Account Name', 'ar' => 'اسم الحساب'],
            'forms.accounts.account_type' => ['en' => 'Account Type', 'ar' => 'نوع الحساب'],
            'forms.accounts.parent_account' => ['en' => 'Parent Account', 'ar' => 'الحساب الأب'],
            'forms.accounts.level' => ['en' => 'Level', 'ar' => 'المستوى'],
            'forms.accounts.allow_manual_entry' => ['en' => 'Allow Manual Entry', 'ar' => 'السماح بالإدخال اليدوي'],
            'forms.accounts.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'forms.accounts.helper.unique_code' => ['en' => 'Unique account code (e.g., 1000, 1100, 2000)', 'ar' => 'رمز حساب فريد (مثل: 1000، 1100، 2000)'],
            'forms.accounts.helper.parent_account' => ['en' => 'Optional: Select a parent account to create a sub-account', 'ar' => 'اختياري: اختر حساباً أباً لإنشاء حساب فرعي'],
            'forms.accounts.helper.level' => ['en' => 'Automatically calculated based on parent', 'ar' => 'يتم حسابه تلقائياً بناءً على الحساب الأب'],
            'forms.accounts.helper.manual_entry' => ['en' => 'Allow manual journal entries to this account', 'ar' => 'السماح بإدخال قيود يدوية لهذا الحساب'],

            // Account Types
            'account_types.asset' => ['en' => 'Asset', 'ar' => 'أصل'],
            'account_types.liability' => ['en' => 'Liability', 'ar' => 'التزام'],
            'account_types.equity' => ['en' => 'Equity', 'ar' => 'حقوق الملكية'],
            'account_types.revenue' => ['en' => 'Revenue', 'ar' => 'الإيرادات'],
            'account_types.expense' => ['en' => 'Expense', 'ar' => 'المصروفات'],

            // Table Columns - Accounts
            'tables.accounts.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.accounts.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.accounts.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.accounts.parent' => ['en' => 'Parent', 'ar' => 'الأب'],
            'tables.accounts.level' => ['en' => 'Level', 'ar' => 'المستوى'],
            'tables.accounts.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.accounts.manual_entry' => ['en' => 'Manual Entry', 'ar' => 'إدخال يدوي'],

            // Asset Resource
            'forms.assets.asset_code' => ['en' => 'Asset Code', 'ar' => 'رمز الأصل'],
            'forms.assets.asset_name' => ['en' => 'Asset Name', 'ar' => 'اسم الأصل'],
            'forms.assets.asset_account' => ['en' => 'Asset Account', 'ar' => 'حساب الأصل'],
            'forms.assets.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.assets.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'forms.assets.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'forms.assets.purchase_cost' => ['en' => 'Purchase Cost', 'ar' => 'تكلفة الشراء'],
            'forms.assets.current_value' => ['en' => 'Current Value', 'ar' => 'القيمة الحالية'],
            'forms.assets.purchase_date' => ['en' => 'Purchase Date', 'ar' => 'تاريخ الشراء'],
            'forms.assets.useful_life_years' => ['en' => 'Useful Life (Years)', 'ar' => 'العمر الإنتاجي (بالسنوات)'],
            'forms.assets.depreciation_rate' => ['en' => 'Depreciation Rate (%)', 'ar' => 'معدل الإهلاك (%)'],
            'forms.assets.location' => ['en' => 'Location', 'ar' => 'الموقع'],
            'forms.assets.serial_number' => ['en' => 'Serial Number', 'ar' => 'الرقم التسلسلي'],

            'asset_types.fixed' => ['en' => 'Fixed Asset', 'ar' => 'أصل ثابت'],
            'asset_types.intangible' => ['en' => 'Intangible Asset', 'ar' => 'أصل غير ملموس'],
            'asset_types.current' => ['en' => 'Current Asset', 'ar' => 'أصل متداول'],
            'asset_types.investment' => ['en' => 'Investment', 'ar' => 'استثمار'],

            'asset_categories.property' => ['en' => 'Property', 'ar' => 'عقار'],
            'asset_categories.equipment' => ['en' => 'Equipment', 'ar' => 'معدات'],
            'asset_categories.vehicle' => ['en' => 'Vehicle', 'ar' => 'مركبة'],
            'asset_categories.furniture' => ['en' => 'Furniture', 'ar' => 'أثاث'],
            'asset_categories.computer' => ['en' => 'Computer', 'ar' => 'حاسوب'],
            'asset_categories.other' => ['en' => 'Other', 'ar' => 'أخرى'],

            'asset_status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'asset_status.deprecated' => ['en' => 'Deprecated', 'ar' => 'مستنفذ'],
            'asset_status.disposed' => ['en' => 'Disposed', 'ar' => 'متصرف به'],
            'asset_status.maintenance' => ['en' => 'Maintenance', 'ar' => 'صيانة'],

            'tables.assets.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.assets.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.assets.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'tables.assets.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.assets.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'tables.assets.purchase_cost' => ['en' => 'Purchase Cost', 'ar' => 'تكلفة الشراء'],
            'tables.assets.current_value' => ['en' => 'Current Value', 'ar' => 'القيمة الحالية'],
            'tables.assets.book_value' => ['en' => 'Book Value', 'ar' => 'القيمة الدفترية'],
            'tables.assets.status' => ['en' => 'Status', 'ar' => 'الحالة'],

            // Voucher Resource
            'forms.vouchers.voucher_type' => ['en' => 'Voucher Type', 'ar' => 'نوع السند'],
            'forms.vouchers.voucher_number' => ['en' => 'Voucher Number', 'ar' => 'رقم السند'],
            'forms.vouchers.voucher_date' => ['en' => 'Voucher Date', 'ar' => 'تاريخ السند'],
            'forms.vouchers.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'forms.vouchers.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'forms.vouchers.helper.voucher_number' => ['en' => 'Auto-generated voucher number', 'ar' => 'رقم سند يتم إنشاؤه تلقائياً'],
            'forms.vouchers.helper.reference' => ['en' => 'External reference number (optional)', 'ar' => 'رقم مرجع خارجي (اختياري)'],

            'voucher_types.payment' => ['en' => 'Payment Voucher', 'ar' => 'سند صرف'],
            'voucher_types.receipt' => ['en' => 'Receipt Voucher', 'ar' => 'سند قبض'],

            'tables.vouchers.voucher_number' => ['en' => 'Voucher Number', 'ar' => 'رقم السند'],
            'tables.vouchers.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.vouchers.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.vouchers.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'tables.vouchers.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'tables.vouchers.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'tables.vouchers.journal_entry' => ['en' => 'Journal Entry', 'ar' => 'قيد اليومية'],
            'tables.vouchers.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],
            'tables.vouchers.has_journal_entry' => ['en' => 'Has Journal Entry', 'ar' => 'يحتوي على قيد'],
            'tables.vouchers.with_journal_entry' => ['en' => 'With Journal Entry', 'ar' => 'مع قيد'],
            'tables.vouchers.without_journal_entry' => ['en' => 'Without Journal Entry', 'ar' => 'بدون قيد'],
            'tables.vouchers.create_journal_entry' => ['en' => 'Create Journal Entry', 'ar' => 'إنشاء قيد اليومية'],

            // Project Resource
            'forms.projects.project_code' => ['en' => 'Project Code', 'ar' => 'رمز المشروع'],
            'forms.projects.project_name' => ['en' => 'Project Name', 'ar' => 'اسم المشروع'],
            'forms.projects.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البدء'],
            'forms.projects.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ الانتهاء'],
            'forms.projects.helper.code' => ['en' => 'Unique code for the project', 'ar' => 'رمز فريد للمشروع'],

            'tables.projects.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.projects.project_name' => ['en' => 'Project Name', 'ar' => 'اسم المشروع'],
            'tables.projects.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البدء'],
            'tables.projects.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ الانتهاء'],

            // Bank Account Resource
            'forms.bank_accounts.bank_name' => ['en' => 'Bank Name', 'ar' => 'اسم البنك'],
            'forms.bank_accounts.account_number' => ['en' => 'Account Number', 'ar' => 'رقم الحساب'],
            'forms.bank_accounts.iban' => ['en' => 'IBAN', 'ar' => 'رقم الآيبان'],
            'forms.bank_accounts.swift_code' => ['en' => 'SWIFT Code', 'ar' => 'رمز السويفت'],
            'forms.bank_accounts.opening_balance' => ['en' => 'Opening Balance', 'ar' => 'الرصيد الافتتاحي'],
            'forms.bank_accounts.current_balance' => ['en' => 'Current Balance', 'ar' => 'الرصيد الحالي'],
            'forms.bank_accounts.helper.account' => ['en' => 'Select the account associated with this bank account', 'ar' => 'اختر الحساب المرتبط بهذا الحساب البنكي'],

            'tables.bank_accounts.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'tables.bank_accounts.account_name' => ['en' => 'Account Name', 'ar' => 'اسم الحساب'],
            'tables.bank_accounts.bank_name' => ['en' => 'Bank Name', 'ar' => 'اسم البنك'],
            'tables.bank_accounts.account_number' => ['en' => 'Account Number', 'ar' => 'رقم الحساب'],
            'tables.bank_accounts.current_balance' => ['en' => 'Current Balance', 'ar' => 'الرصيد الحالي'],
            'tables.bank_accounts.reconcile' => ['en' => 'Reconcile', 'ar' => 'التسوية'],

            // ============================================
            // BRANCH RESOURCE TRANSLATIONS
            // ============================================

            'forms.branches.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.branches.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.branches.parent_branch' => ['en' => 'Parent Branch', 'ar' => 'الفرع الأب'],
            'forms.branches.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'forms.branches.address' => ['en' => 'Address', 'ar' => 'العنوان'],
            'forms.branches.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'forms.branches.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'forms.branches.metadata' => ['en' => 'Metadata', 'ar' => 'البيانات الوصفية'],
            'forms.branches.helper.code' => ['en' => 'Unique code for the branch', 'ar' => 'رمز فريد للفرع'],
            'forms.branches.helper.parent' => ['en' => 'Optional: Select a parent branch if this is a sub-branch', 'ar' => 'اختياري: اختر فرعاً أباً إذا كان هذا فرعاً فرعياً'],
            'forms.branches.helper.metadata' => ['en' => 'Additional flexible data (optional)', 'ar' => 'بيانات مرنة إضافية (اختياري)'],

            'tables.branches.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.branches.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.branches.parent_branch' => ['en' => 'Parent Branch', 'ar' => 'الفرع الأب'],
            'tables.branches.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'tables.branches.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'tables.branches.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.branches.users' => ['en' => 'Users', 'ar' => 'المستخدمون'],

            // Status Options
            'status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'status.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],

            // ============================================
            // CUSTOMER RESOURCE TRANSLATIONS
            // ============================================

            'forms.customers.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.customers.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.customers.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'forms.customers.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'forms.customers.address' => ['en' => 'Address', 'ar' => 'العنوان'],
            'forms.customers.city' => ['en' => 'City', 'ar' => 'المدينة'],
            'forms.customers.state' => ['en' => 'State', 'ar' => 'الولاية'],
            'forms.customers.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'forms.customers.postal_code' => ['en' => 'Postal Code', 'ar' => 'الرمز البريدي'],
            'forms.customers.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'forms.customers.credit_limit' => ['en' => 'Credit Limit', 'ar' => 'حد الائتمان'],
            'forms.customers.helper.code' => ['en' => 'Unique customer code', 'ar' => 'رمز عميل فريد'],

            'tables.customers.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.customers.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.customers.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'tables.customers.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'tables.customers.credit_limit' => ['en' => 'Credit Limit', 'ar' => 'حد الائتمان'],
            'tables.customers.orders' => ['en' => 'Orders', 'ar' => 'الطلبات'],

            // ============================================
            // ORDER RESOURCE TRANSLATIONS
            // ============================================

            'forms.orders.order_number' => ['en' => 'Order Number', 'ar' => 'رقم الطلب'],
            'forms.orders.order_date' => ['en' => 'Order Date', 'ar' => 'تاريخ الطلب'],
            'forms.orders.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'forms.orders.subtotal' => ['en' => 'Subtotal', 'ar' => 'المجموع الفرعي'],
            'forms.orders.tax_amount' => ['en' => 'Tax Amount', 'ar' => 'مبلغ الضريبة'],
            'forms.orders.discount_amount' => ['en' => 'Discount Amount', 'ar' => 'مبلغ الخصم'],
            'forms.orders.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'forms.orders.product' => ['en' => 'Product', 'ar' => 'المنتج'],
            'forms.orders.quantity' => ['en' => 'Quantity', 'ar' => 'الكمية'],
            'forms.orders.unit_price' => ['en' => 'Unit Price', 'ar' => 'سعر الوحدة'],
            'forms.orders.discount' => ['en' => 'Discount', 'ar' => 'الخصم'],

            'order_status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'order_status.processing' => ['en' => 'Processing', 'ar' => 'قيد المعالجة'],
            'order_status.completed' => ['en' => 'Completed', 'ar' => 'مكتمل'],
            'order_status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغي'],
            'order_status.refunded' => ['en' => 'Refunded', 'ar' => 'مسترد'],

            'tables.orders.order_number' => ['en' => 'Order #', 'ar' => 'رقم الطلب'],
            'tables.orders.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.orders.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'tables.orders.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],

            // ============================================
            // INVOICE RESOURCE TRANSLATIONS
            // ============================================

            'forms.invoices.invoice_number' => ['en' => 'Invoice Number', 'ar' => 'رقم الفاتورة'],
            'forms.invoices.invoice_date' => ['en' => 'Invoice Date', 'ar' => 'تاريخ الفاتورة'],
            'forms.invoices.order' => ['en' => 'Order', 'ar' => 'الطلب'],
            'forms.invoices.due_date' => ['en' => 'Due Date', 'ar' => 'تاريخ الاستحقاق'],
            'forms.invoices.paid_at' => ['en' => 'Paid At', 'ar' => 'تم الدفع في'],

            'invoice_status.draft' => ['en' => 'Draft', 'ar' => 'مسودة'],
            'invoice_status.sent' => ['en' => 'Sent', 'ar' => 'مرسلة'],
            'invoice_status.paid' => ['en' => 'Paid', 'ar' => 'مدفوعة'],
            'invoice_status.partial' => ['en' => 'Partial', 'ar' => 'جزئية'],
            'invoice_status.overdue' => ['en' => 'Overdue', 'ar' => 'متأخرة'],
            'invoice_status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغاة'],

            'tables.invoices.invoice_number' => ['en' => 'Invoice #', 'ar' => 'رقم الفاتورة'],
            'tables.invoices.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.invoices.order' => ['en' => 'Order', 'ar' => 'الطلب'],
            'tables.invoices.due_date' => ['en' => 'Due Date', 'ar' => 'تاريخ الاستحقاق'],
            'tables.invoices.paid_at' => ['en' => 'Paid At', 'ar' => 'تم الدفع في'],
            'tables.invoices.overdue_invoices' => ['en' => 'Overdue Invoices', 'ar' => 'الفواتير المتأخرة'],

            // ============================================
            // PRODUCT RESOURCE TRANSLATIONS
            // ============================================

            'forms.products.sku' => ['en' => 'SKU', 'ar' => 'رمز المخزون'],
            'forms.products.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.products.slug' => ['en' => 'Slug', 'ar' => 'الرابط'],
            'forms.products.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.products.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'forms.products.brand' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
            'forms.products.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.products.price' => ['en' => 'Price', 'ar' => 'السعر'],
            'forms.products.cost' => ['en' => 'Cost', 'ar' => 'التكلفة'],
            'forms.products.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'forms.products.stock_quantity' => ['en' => 'Stock Quantity', 'ar' => 'كمية المخزون'],
            'forms.products.track_inventory' => ['en' => 'Track Inventory', 'ar' => 'تتبع المخزون'],
            'forms.products.warehouse' => ['en' => 'Warehouse', 'ar' => 'المستودع'],
            'forms.products.quantity' => ['en' => 'Quantity', 'ar' => 'الكمية'],
            'forms.products.min_stock_level' => ['en' => 'Min Stock Level', 'ar' => 'الحد الأدنى للمخزون'],
            'forms.products.max_stock_level' => ['en' => 'Max Stock Level', 'ar' => 'الحد الأقصى للمخزون'],
            'forms.products.batch_number' => ['en' => 'Batch Number', 'ar' => 'رقم الدفعة'],
            'forms.products.manufacturing_date' => ['en' => 'Manufacturing Date', 'ar' => 'تاريخ التصنيع'],
            'forms.products.expiry_date' => ['en' => 'Expiry Date', 'ar' => 'تاريخ الانتهاء'],
            'forms.products.helper.sku' => ['en' => 'Stock Keeping Unit - unique identifier', 'ar' => 'وحدة حفظ المخزون - معرف فريد'],
            'forms.products.helper.slug' => ['en' => 'Auto-generated from name', 'ar' => 'يتم إنشاؤه تلقائياً من الاسم'],
            'forms.products.helper.cost' => ['en' => 'Cost price for profit calculation', 'ar' => 'سعر التكلفة لحساب الربح'],

            'product_types.product' => ['en' => 'Product', 'ar' => 'منتج'],
            'product_types.service' => ['en' => 'Service', 'ar' => 'خدمة'],

            'tables.products.sku' => ['en' => 'SKU', 'ar' => 'رمز المخزون'],
            'tables.products.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.products.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.products.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'tables.products.brand' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
            'tables.products.price' => ['en' => 'Price', 'ar' => 'السعر'],
            'tables.products.stock' => ['en' => 'Stock', 'ar' => 'المخزون'],

            // ============================================
            // CATEGORY RESOURCE TRANSLATIONS
            // ============================================

            'forms.categories.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.categories.slug' => ['en' => 'Slug', 'ar' => 'الرابط'],
            'forms.categories.parent_category' => ['en' => 'Parent Category', 'ar' => 'الفئة الأب'],
            'forms.categories.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.categories.image' => ['en' => 'Image', 'ar' => 'الصورة'],
            'forms.categories.sort_order' => ['en' => 'Sort Order', 'ar' => 'ترتيب العرض'],
            'forms.categories.helper.slug' => ['en' => 'Auto-generated from name', 'ar' => 'يتم إنشاؤه تلقائياً من الاسم'],

            'tables.categories.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.categories.parent' => ['en' => 'Parent', 'ar' => 'الأب'],
            'tables.categories.products' => ['en' => 'Products', 'ar' => 'المنتجات'],

            // ============================================
            // BRAND RESOURCE TRANSLATIONS
            // ============================================

            'forms.brands.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.brands.slug' => ['en' => 'Slug', 'ar' => 'الرابط'],
            'forms.brands.logo' => ['en' => 'Logo', 'ar' => 'الشعار'],
            'forms.brands.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.brands.helper.slug' => ['en' => 'Auto-generated from name', 'ar' => 'يتم إنشاؤه تلقائياً من الاسم'],

            'tables.brands.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.brands.products' => ['en' => 'Products', 'ar' => 'المنتجات'],

            // ============================================
            // JOURNAL ENTRY RESOURCE TRANSLATIONS
            // ============================================

            'forms.journal_entries.journal' => ['en' => 'Journal', 'ar' => 'اليومية'],
            'forms.journal_entries.entry_number' => ['en' => 'Entry Number', 'ar' => 'رقم القيد'],
            'forms.journal_entries.entry_date' => ['en' => 'Entry Date', 'ar' => 'تاريخ القيد'],
            'forms.journal_entries.fiscal_year' => ['en' => 'Fiscal Year', 'ar' => 'السنة المالية'],
            'forms.journal_entries.period' => ['en' => 'Period', 'ar' => 'الفترة'],
            'forms.journal_entries.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'forms.journal_entries.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.journal_entries.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'forms.journal_entries.cost_center' => ['en' => 'Cost Center', 'ar' => 'مركز التكلفة'],
            'forms.journal_entries.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.journal_entries.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'forms.journal_entries.debit' => ['en' => 'Debit', 'ar' => 'مدين'],
            'forms.journal_entries.credit' => ['en' => 'Credit', 'ar' => 'دائن'],
            'forms.journal_entries.debit_amount' => ['en' => 'Debit Amount', 'ar' => 'المبلغ المدين'],
            'forms.journal_entries.credit_amount' => ['en' => 'Credit Amount', 'ar' => 'المبلغ الدائن'],
            'forms.journal_entries.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'forms.journal_entries.exchange_rate' => ['en' => 'Exchange Rate', 'ar' => 'سعر الصرف'],
            'forms.journal_entries.base_amount' => ['en' => 'Base Amount', 'ar' => 'المبلغ الأساسي'],
            'forms.journal_entries.project' => ['en' => 'Project', 'ar' => 'المشروع'],
            'forms.journal_entries.helper.entry_number' => ['en' => 'Auto-generated entry number', 'ar' => 'رقم قيد يتم إنشاؤه تلقائياً'],
            'forms.journal_entries.helper.reference' => ['en' => 'External reference number (optional)', 'ar' => 'رقم مرجع خارجي (اختياري)'],
            'forms.journal_entries.helper.minimum_lines' => ['en' => 'At least two lines are required. Total debits must equal total credits.', 'ar' => 'يُطلب خطان على الأقل. يجب أن يساوي إجمالي المدين إجمالي الدائن.'],
            'forms.journal_entries.add_line' => ['en' => 'Add Line', 'ar' => 'إضافة سطر'],

            'tables.journal_entries.entry_number' => ['en' => 'Entry Number', 'ar' => 'رقم القيد'],
            'tables.journal_entries.journal' => ['en' => 'Journal', 'ar' => 'اليومية'],
            'tables.journal_entries.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.journal_entries.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'tables.journal_entries.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.journal_entries.cost_center' => ['en' => 'Cost Center', 'ar' => 'مركز التكلفة'],
            'tables.journal_entries.total_debits' => ['en' => 'Total Debits', 'ar' => 'إجمالي المدين'],
            'tables.journal_entries.total_credits' => ['en' => 'Total Credits', 'ar' => 'إجمالي الدائن'],
            'tables.journal_entries.posted' => ['en' => 'Posted', 'ar' => 'مُسجل'],
            'tables.journal_entries.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],

            // ============================================
            // REPORT PAGE TRANSLATIONS
            // ============================================

            // Trial Balance Report
            'reports.trial_balance.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'reports.trial_balance.account_name' => ['en' => 'Account Name', 'ar' => 'اسم الحساب'],
            'reports.trial_balance.account_type' => ['en' => 'Type', 'ar' => 'النوع'],
            'reports.trial_balance.debits' => ['en' => 'Debits', 'ar' => 'مدين'],
            'reports.trial_balance.credits' => ['en' => 'Credits', 'ar' => 'دائن'],
            'reports.trial_balance.balance' => ['en' => 'Balance', 'ar' => 'الرصيد'],

            // General Ledger Report
            'reports.general_ledger.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'reports.general_ledger.entry_number' => ['en' => 'Entry #', 'ar' => 'رقم القيد'],
            'reports.general_ledger.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'reports.general_ledger.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'reports.general_ledger.debit' => ['en' => 'Debit', 'ar' => 'مدين'],
            'reports.general_ledger.credit' => ['en' => 'Credit', 'ar' => 'دائن'],
            'reports.general_ledger.balance' => ['en' => 'Balance', 'ar' => 'الرصيد'],
            'reports.general_ledger.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'reports.general_ledger.cost_center' => ['en' => 'Cost Center', 'ar' => 'مركز التكلفة'],

            // Income Statement Report
            'reports.income_statement.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'reports.income_statement.account_name' => ['en' => 'Account Name', 'ar' => 'اسم الحساب'],
            'reports.income_statement.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],

            // Changes in Equity Report
            'reports.changes_in_equity.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'reports.changes_in_equity.account_code' => ['en' => 'Account Code', 'ar' => 'رمز الحساب'],
            'reports.changes_in_equity.account_name' => ['en' => 'Account Name', 'ar' => 'اسم الحساب'],
            'reports.changes_in_equity.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'reports.changes_in_equity.movement' => ['en' => 'Movement', 'ar' => 'الحركة'],

            // Accounts Payable Aging Reports
            'reports.accounts_payable.supplier' => ['en' => 'Supplier', 'ar' => 'المورد'],
            'reports.accounts_payable.current' => ['en' => '0-30 Days', 'ar' => '0-30 يوم'],
            'reports.accounts_payable.days_31_60' => ['en' => '31-60 Days', 'ar' => '31-60 يوم'],
            'reports.accounts_payable.days_61_90' => ['en' => '61-90 Days', 'ar' => '61-90 يوم'],
            'reports.accounts_payable.over_90' => ['en' => 'Over 90 Days', 'ar' => 'أكثر من 90 يوم'],
            'reports.accounts_payable.overdue_amount' => ['en' => 'Overdue Amount', 'ar' => 'المبلغ المتأخر'],
            'reports.accounts_payable.days_overdue' => ['en' => 'Days Overdue', 'ar' => 'أيام التأخير'],
            'reports.accounts_payable.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],

            // Journal Entries by Year Report
            'reports.journal_entries_by_year.month' => ['en' => 'Month', 'ar' => 'الشهر'],
            'reports.journal_entries_by_year.entry_count' => ['en' => 'Entry Count', 'ar' => 'عدد القيود'],

            // Comparisons Report
            'reports.comparisons.period_b_from_date' => ['en' => 'Period B From Date', 'ar' => 'تاريخ بداية الفترة ب'],
            'reports.comparisons.period_b_to_date' => ['en' => 'Period B To Date', 'ar' => 'تاريخ نهاية الفترة ب'],

            // Financial Performance Report
            'reports.financial_performance.kpi' => ['en' => 'KPI', 'ar' => 'مؤشر الأداء الرئيسي'],

            // ============================================
            // COMMON SECTION TITLES
            // ============================================

            'sections.basic_information' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'sections.contact_information' => ['en' => 'Contact Information', 'ar' => 'معلومات الاتصال'],
            'sections.additional_information' => ['en' => 'Additional Information', 'ar' => 'معلومات إضافية'],
            'sections.pricing_inventory' => ['en' => 'Pricing & Inventory', 'ar' => 'التسعير والمخزون'],
            'sections.warehouses' => ['en' => 'Warehouses', 'ar' => 'المستودعات'],
            'sections.batches' => ['en' => 'Batches', 'ar' => 'الدفعات'],
            'sections.financial' => ['en' => 'Financial', 'ar' => 'المالية'],
            'sections.financial_information' => ['en' => 'Financial Information', 'ar' => 'المعلومات المالية'],
            'sections.address' => ['en' => 'Address', 'ar' => 'العنوان'],
            'sections.entry_information' => ['en' => 'Entry Information', 'ar' => 'معلومات القيد'],
            'sections.journal_entry_lines' => ['en' => 'Journal Entry Lines', 'ar' => 'أسطر قيد اليومية'],
            'sections.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'sections.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],
            'sections.voucher_information' => ['en' => 'Voucher Information', 'ar' => 'معلومات السند'],
            'sections.bank_account_information' => ['en' => 'Bank Account Information', 'ar' => 'معلومات الحساب البنكي'],
            'sections.project_information' => ['en' => 'Project Information', 'ar' => 'معلومات المشروع'],
            'sections.order_information' => ['en' => 'Order Information', 'ar' => 'معلومات الطلب'],
            'sections.order_items' => ['en' => 'Order Items', 'ar' => 'عناصر الطلب'],
            'sections.invoice_information' => ['en' => 'Invoice Information', 'ar' => 'معلومات الفاتورة'],
            'sections.invoice_items' => ['en' => 'Invoice Items', 'ar' => 'عناصر الفاتورة'],
            'sections.location_details' => ['en' => 'Location & Details', 'ar' => 'الموقع والتفاصيل'],
            'sections.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            // ============================================
// SIDEBAR / NAVIGATION TRANSLATIONS
// ============================================

// Accounting group
'sidebar.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],

// Accounting pages
'sidebar.accounting.accounts_tree' => ['en' => 'Accounts Tree Page', 'ar' => 'شجرة الحسابات'],
'sidebar.accounting.trial_balance' => ['en' => 'Trial Balance Page', 'ar' => 'ميزان المراجعة'],
'sidebar.accounting.sales_report' => ['en' => 'Sales Report Page', 'ar' => 'تقرير المبيعات'],
'sidebar.accounting.orders_report' => ['en' => 'Orders Report Page', 'ar' => 'تقرير الطلبات'],
'sidebar.accounting.invoices_report' => ['en' => 'Invoices Report Page', 'ar' => 'تقرير الفواتير'],

// If you have a journal menu item showing as "sidebar.accounting.journal"
'sidebar.accounting.journal' => ['en' => 'Journal', 'ar' => 'اليومية'],

// MainCore warehouse item showing as "sidebar.maincore.warehouse"
'sidebar.maincore.warehouse' => ['en' => 'Warehouse', 'ar' => 'المستودع'],

            // ============================================
            // FILTER LABELS
            // ============================================

            'filters.all' => ['en' => 'All', 'ar' => 'الكل'],
            'filters.active_only' => ['en' => 'Active only', 'ar' => 'النشطة فقط'],
            'filters.inactive_only' => ['en' => 'Inactive only', 'ar' => 'غير النشطة فقط'],
            'filters.from_date' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'filters.to_date' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'filters.stock_from' => ['en' => 'Stock From', 'ar' => 'المخزون من'],
            'filters.stock_to' => ['en' => 'Stock To', 'ar' => 'المخزون إلى'],
            'filters.parent_account' => ['en' => 'Parent Account', 'ar' => 'الحساب الأب'],
            'filters.parent_branch' => ['en' => 'Parent Branch', 'ar' => 'الفرع الأب'],
            'filters.category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'filters.brand' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
            'filters.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'filters.until' => ['en' => 'Until', 'ar' => 'حتى'],

            // ============================================
            // OTHER COMMON LABELS
            // ============================================

            'labels.key' => ['en' => 'Key', 'ar' => 'المفتاح'],
            'labels.value' => ['en' => 'Value', 'ar' => 'القيمة'],
            'labels.roles' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'labels.permission' => ['en' => 'Permission', 'ar' => 'الصلاحية'],
            'labels.permission_name' => ['en' => 'Permission Name', 'ar' => 'اسم الصلاحية'],
            'labels.guard' => ['en' => 'Guard', 'ar' => 'الحارس'],
            'labels.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],
            'labels.role' => ['en' => 'Role', 'ar' => 'الدور'],
            'labels.role_name' => ['en' => 'Role Name', 'ar' => 'اسم الدور'],
            'labels.translation_key' => ['en' => 'Translation Key', 'ar' => 'مفتاح الترجمة'],
            'labels.group' => ['en' => 'Group', 'ar' => 'المجموعة'],
            'labels.language' => ['en' => 'Language', 'ar' => 'اللغة'],
            'labels.translation_value' => ['en' => 'Translation Value', 'ar' => 'قيمة الترجمة'],
            'labels.theme_name' => ['en' => 'Theme Name', 'ar' => 'اسم السمة'],
            'labels.set_as_default_theme' => ['en' => 'Set as Default Theme', 'ar' => 'تعيين كسمة افتراضية'],
            'labels.primary_color' => ['en' => 'Primary Color', 'ar' => 'اللون الأساسي'],
            'labels.secondary_color' => ['en' => 'Secondary Color', 'ar' => 'اللون الثانوي'],
            'labels.accent_color' => ['en' => 'Accent Color', 'ar' => 'لون التمييز'],
            'labels.light_mode_logo' => ['en' => 'Light Mode Logo', 'ar' => 'شعار الوضع الفاتح'],
            'labels.dark_mode_logo' => ['en' => 'Dark Mode Logo', 'ar' => 'شعار الوضع الداكن'],
            'labels.light_logo' => ['en' => 'Light Logo', 'ar' => 'الشعار الفاتح'],
            'labels.default' => ['en' => 'Default', 'ar' => 'افتراضي'],
            'labels.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'labels.base_currency' => ['en' => 'Base Currency', 'ar' => 'العملة الأساسية'],
            'labels.target_currency' => ['en' => 'Target Currency', 'ar' => 'العملة المستهدفة'],
            'labels.valid_from' => ['en' => 'Valid From', 'ar' => 'صالح من'],
            'labels.base' => ['en' => 'Base', 'ar' => 'الأساسي'],
            'labels.target' => ['en' => 'Target', 'ar' => 'المستهدف'],
            'labels.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'labels.name_en' => ['en' => 'Name (EN)', 'ar' => 'الاسم (إنجليزي)'],
            'labels.native_name' => ['en' => 'Native Name', 'ar' => 'الاسم الأصلي'],
            'labels.direction' => ['en' => 'Direction', 'ar' => 'الاتجاه'],
            'labels.left_to_right' => ['en' => 'Left to Right', 'ar' => 'من اليسار إلى اليمين'],
            'labels.right_to_left' => ['en' => 'Right to Left', 'ar' => 'من اليمين إلى اليسار'],
            'labels.helper.default_language' => ['en' => 'Only one language should be default.', 'ar' => 'يجب أن تكون لغة واحدة فقط هي الافتراضية.'],
            'labels.helper.translation_key' => ['en' => 'e.g., dashboard.welcome, auth.login', 'ar' => 'مثال: dashboard.welcome, auth.login'],
            'labels.helper.group' => ['en' => 'Group name like: dashboard, auth, validation, etc.', 'ar' => 'اسم المجموعة مثل: dashboard, auth, validation, إلخ.'],
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

        $this->command->info("Complete pages translations seeded: {$created} created, {$updated} updated.");
    }
}

