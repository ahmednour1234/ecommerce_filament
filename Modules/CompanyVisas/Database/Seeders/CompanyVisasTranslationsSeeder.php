<?php

namespace Modules\CompanyVisas\Database\Seeders;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class CompanyVisasTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Company Visas translations...');

        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping translations.');
            return;
        }

        $translations = [
            'company_visas.menu.group' => ['en' => 'Visas', 'ar' => 'التأشيرات'],
            'company_visas.menu.requests' => ['en' => 'Visa Requests', 'ar' => 'طلبات التأشيرات'],
            'company_visas.menu.contracts' => ['en' => 'Company Visa Contracts', 'ar' => 'عقود استقدام الشركة'],
            'sidebar.company_visas.requests' => ['en' => 'Visa Requests', 'ar' => 'طلبات التأشيرات'],
            'sidebar.company_visas.contracts' => ['en' => 'Company Visa Contracts', 'ar' => 'عقود استقدام الشركة'],

            'company_visas.fields.code' => ['en' => 'Request Code', 'ar' => 'رمز الطلب'],
            'company_visas.fields.request_date' => ['en' => 'Request Date', 'ar' => 'تاريخ الطلب'],
            'company_visas.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'company_visas.fields.nationality' => ['en' => 'Nationality', 'ar' => 'الجنسية'],
            'company_visas.fields.gender' => ['en' => 'Gender', 'ar' => 'الجنس'],
            'company_visas.fields.workers_count' => ['en' => 'Workers Count', 'ar' => 'عدد العمالة'],
            'company_visas.fields.visa_number' => ['en' => 'Visa Number', 'ar' => 'رقم التأشيرة'],
            'company_visas.fields.used_count' => ['en' => 'Used', 'ar' => 'مستخدم'],
            'company_visas.fields.remaining_count' => ['en' => 'Remaining', 'ar' => 'متبقي'],
            'company_visas.fields.used_remaining' => ['en' => 'Used/Remaining', 'ar' => 'مستخدم/متبقي'],
            'company_visas.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'company_visas.fields.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'company_visas.fields.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'company_visas.fields.contract_date' => ['en' => 'Contract Date', 'ar' => 'تاريخ العقد'],
            'company_visas.fields.visa_request' => ['en' => 'Visa Request', 'ar' => 'طلب التأشيرة'],
            'company_visas.fields.agent' => ['en' => 'Agent', 'ar' => 'الوكيل'],
            'company_visas.fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'company_visas.fields.workers_required' => ['en' => 'Workers Required', 'ar' => 'عدد العمالة المطلوبة'],
            'company_visas.fields.linked_workers_count' => ['en' => 'Linked Workers Count', 'ar' => 'عدد العمالة المرتبطة'],
            'company_visas.fields.expense_account' => ['en' => 'Expense Account', 'ar' => 'حساب المصروف'],
            'company_visas.fields.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'company_visas.fields.includes_vat' => ['en' => 'Includes VAT', 'ar' => 'يشمل ضريبة القيمة المضافة'],
            'company_visas.fields.expense_date' => ['en' => 'Expense Date', 'ar' => 'تاريخ المصروف'],
            'company_visas.fields.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'company_visas.fields.invoice_no' => ['en' => 'Invoice No', 'ar' => 'رقم الفاتورة'],
            'company_visas.fields.attachment' => ['en' => 'Attachment', 'ar' => 'المرفق'],
            'company_visas.fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'company_visas.fields.cost_per_worker' => ['en' => 'Cost Per Worker', 'ar' => 'تكلفة العامل الواحد'],
            'company_visas.fields.total_cost' => ['en' => 'Total Cost', 'ar' => 'التكلفة الإجمالية'],
            'company_visas.fields.due_date' => ['en' => 'Due Date', 'ar' => 'الاستحقاق'],
            'company_visas.fields.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'company_visas.fields.file' => ['en' => 'File', 'ar' => 'الملف'],
            'company_visas.fields.worker_name' => ['en' => 'Worker Name', 'ar' => 'اسم العامل'],
            'company_visas.fields.passport_number' => ['en' => 'Passport Number', 'ar' => 'رقم الجواز'],
            'company_visas.fields.workers' => ['en' => 'Workers', 'ar' => 'العمالة'],
            'company_visas.fields.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],

            'company_visas.gender.male' => ['en' => 'Male', 'ar' => 'ذكر'],
            'company_visas.gender.female' => ['en' => 'Female', 'ar' => 'أنثى'],

            'company_visas.status.draft' => ['en' => 'Draft', 'ar' => 'مسودة'],
            'company_visas.status.paid' => ['en' => 'Paid', 'ar' => 'مسدد'],
            'company_visas.status.completed' => ['en' => 'Completed', 'ar' => 'مكتمل'],
            'company_visas.status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'company_visas.status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'company_visas.status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغي'],

            'company_visas.actions.create_new_request' => ['en' => 'Create New Visa Request', 'ar' => 'إضافة طلب تأشيرة جديد'],
            'company_visas.actions.create_new_contract' => ['en' => 'Create New Contract', 'ar' => 'إنشاء عقد استقدام جديد'],
            'company_visas.actions.link_workers' => ['en' => 'Link Workers', 'ar' => 'ربط عمالة'],
            'company_visas.actions.add_expense' => ['en' => 'Add Expense', 'ar' => 'إضافة مصروف'],
            'company_visas.actions.update_status' => ['en' => 'Update Status', 'ar' => 'تحديث الحالة'],
            'company_visas.actions.contract_cost' => ['en' => 'Contract Cost', 'ar' => 'تكلفة الاستقدام'],
            'company_visas.actions.available_actions' => ['en' => 'Available Actions', 'ar' => 'الإجراءات المتاحة'],

            'company_visas.sections.basic_data' => ['en' => 'Basic Data', 'ar' => 'البيانات الأساسية'],
            'company_visas.sections.contract_info' => ['en' => 'Contract Information', 'ar' => 'معلومات العقد الأساسية'],
            'company_visas.sections.workers_info' => ['en' => 'Workers Information', 'ar' => 'معلومات العمالة'],

            'company_visas.tabs.details' => ['en' => 'Contract Details', 'ar' => 'تفاصيل العقد'],
            'company_visas.tabs.workers' => ['en' => 'Linked Workers', 'ar' => 'العمالة المرتبطة'],
            'company_visas.tabs.expenses' => ['en' => 'Expenses', 'ar' => 'المصروفات'],
            'company_visas.tabs.costs' => ['en' => 'Contract Costs', 'ar' => 'تكلفة الاستقدام'],
            'company_visas.tabs.documents' => ['en' => 'Documents', 'ar' => 'المستندات'],

            'company_visas.helpers.workers_required' => ['en' => 'Must not exceed remaining visa balance', 'ar' => 'يجب ألا يتجاوز الرصيد المتبقي من التأشيرة'],

            'company_visas.messages.finance_entry_info' => ['en' => 'A journal entry will be created automatically: Buyer account (Debit) and Agent account (Credit).', 'ar' => 'سيتم إنشاء قيد محاسبي تلقائياً: حساب المشترى (مدين) وحساب الوكيل (دائن).'],

            'company_visas.actions.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'company_visas.actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'company_visas.actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'company_visas.actions.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'company_visas.actions.view' => ['en' => 'View', 'ar' => 'عرض'],
            'company_visas.actions.create' => ['en' => 'Create', 'ar' => 'إنشاء'],
            'company_visas.actions.load_available_workers' => ['en' => 'Load Available Workers', 'ar' => 'تحميل العمالة المتاحة'],
            'company_visas.actions.remove_worker' => ['en' => 'Remove Worker', 'ar' => 'إزالة عامل'],
            'company_visas.actions.upload_document' => ['en' => 'Upload Document', 'ar' => 'رفع مستند'],

            'company_visas.messages.worker_linked_success' => ['en' => 'Workers linked successfully', 'ar' => 'تم ربط العمالة بنجاح'],
            'company_visas.messages.expense_added_success' => ['en' => 'Expense added successfully', 'ar' => 'تم إضافة المصروف بنجاح'],
            'company_visas.messages.status_updated_success' => ['en' => 'Status updated successfully', 'ar' => 'تم تحديث الحالة بنجاح'],
            'company_visas.messages.cost_added_success' => ['en' => 'Contract cost added successfully', 'ar' => 'تم إضافة تكلفة العقد بنجاح'],
            'company_visas.messages.document_uploaded_success' => ['en' => 'Document uploaded successfully', 'ar' => 'تم رفع المستند بنجاح'],

            'company_visas.validation.workers_required_exceeds_balance' => ['en' => 'Workers required exceeds remaining visa balance', 'ar' => 'عدد العمالة المطلوبة يتجاوز الرصيد المتبقي من التأشيرة'],
            'company_visas.validation.no_workers_available' => ['en' => 'No workers available matching the criteria', 'ar' => 'لا توجد عمالة متاحة تطابق المعايير'],
        ];

        $created = 0;
        foreach ($translations as $key => $values) {
            if (isset($values['en'])) {
                Translation::updateOrCreate(
                    ['key' => $key, 'group' => 'dashboard', 'language_id' => $english->id],
                    ['value' => $values['en']]
                );
                $created++;
            }
            if (isset($values['ar'])) {
                Translation::updateOrCreate(
                    ['key' => $key, 'group' => 'dashboard', 'language_id' => $arabic->id],
                    ['value' => $values['ar']]
                );
                $created++;
            }
        }

        $this->command->info("✓ Company Visas translations created: {$created} entries");
    }
}
