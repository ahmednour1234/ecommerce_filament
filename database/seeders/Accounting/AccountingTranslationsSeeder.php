<?php

namespace Database\Seeders\Accounting;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class AccountingTranslationsSeeder extends Seeder
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
            // Navigation
            'navigation.accounting' => ['en' => 'Accounting', 'ar' => 'المحاسبة'],
            'navigation.journal_entries' => ['en' => 'Journal Entries', 'ar' => 'قيود اليومية'],
            'navigation.accounts' => ['en' => 'Chart of Accounts', 'ar' => 'دليل الحسابات'],
            'navigation.journals' => ['en' => 'Journals', 'ar' => 'اليوميات'],
            'navigation.vouchers' => ['en' => 'Vouchers', 'ar' => 'السندات'],
            'navigation.fiscal_years' => ['en' => 'Fiscal Years', 'ar' => 'السنوات المالية'],
            'navigation.periods' => ['en' => 'Periods', 'ar' => 'الفترات'],
            'navigation.projects' => ['en' => 'Projects', 'ar' => 'المشاريع'],
            'navigation.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],

            // Journal Entry
            'accounting.entry_information' => ['en' => 'Entry Information', 'ar' => 'معلومات القيد'],
            'accounting.journal_entry_lines' => ['en' => 'Journal Entry Lines', 'ar' => 'بنود القيد'],
            'accounting.journal' => ['en' => 'Journal', 'ar' => 'اليومية'],
            'accounting.entry_number' => ['en' => 'Entry Number', 'ar' => 'رقم القيد'],
            'accounting.entry_date' => ['en' => 'Entry Date', 'ar' => 'تاريخ القيد'],
            'accounting.fiscal_year' => ['en' => 'Fiscal Year', 'ar' => 'السنة المالية'],
            'accounting.period' => ['en' => 'Period', 'ar' => 'الفترة'],
            'accounting.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
            'accounting.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'accounting.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'accounting.cost_center' => ['en' => 'Cost Center', 'ar' => 'مركز التكلفة'],
            'accounting.project' => ['en' => 'Project', 'ar' => 'المشروع'],
            'accounting.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'accounting.account' => ['en' => 'Account', 'ar' => 'الحساب'],
            'accounting.debit' => ['en' => 'Debit', 'ar' => 'مدين'],
            'accounting.credit' => ['en' => 'Credit', 'ar' => 'دائن'],
            'accounting.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'accounting.exchange_rate' => ['en' => 'Exchange Rate', 'ar' => 'سعر الصرف'],
            'accounting.amount_in_base' => ['en' => 'Amount in Base', 'ar' => 'المبلغ بالعملة الأساسية'],
            'accounting.total_debit' => ['en' => 'Total Debit', 'ar' => 'إجمالي المدين'],
            'accounting.total_credit' => ['en' => 'Total Credit', 'ar' => 'إجمالي الدائن'],
            'accounting.difference' => ['en' => 'Difference', 'ar' => 'الفرق'],
            'accounting.add_row' => ['en' => 'Add Row', 'ar' => 'إضافة صف'],
            'accounting.add_rows' => ['en' => 'Add :count Rows', 'ar' => 'إضافة :count صفوف'],
            'accounting.delete_selected' => ['en' => 'Delete Selected', 'ar' => 'حذف المحدد'],
            'accounting.duplicate' => ['en' => 'Duplicate', 'ar' => 'نسخ'],
            'accounting.entries_not_balanced' => ['en' => 'Entries are not balanced', 'ar' => 'القيد غير متوازن'],
            'accounting.posted' => ['en' => 'Posted', 'ar' => 'مقيد'],
            'accounting.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],
            'accounting.all' => ['en' => 'All', 'ar' => 'الكل'],
            'accounting.posted_only' => ['en' => 'Posted only', 'ar' => 'المقيدة فقط'],
            'accounting.unposted_only' => ['en' => 'Unposted only', 'ar' => 'غير المقيدة فقط'],
            'accounting.from_date' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'accounting.to_date' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'accounting.submit_for_approval' => ['en' => 'Submit for Approval', 'ar' => 'إرسال للموافقة'],
            'accounting.approve' => ['en' => 'Approve', 'ar' => 'موافقة'],
            'accounting.reject' => ['en' => 'Reject', 'ar' => 'رفض'],
            'accounting.post' => ['en' => 'Post', 'ar' => 'تسجيل'],
            'accounting.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'accounting.export_pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],
            'accounting.export_excel' => ['en' => 'Export Excel', 'ar' => 'تصدير Excel'],
            'accounting.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'accounting.rejection_reason' => ['en' => 'Rejection Reason', 'ar' => 'سبب الرفض'],
            'accounting.cannot_submit' => ['en' => 'Entry cannot be submitted in current status.', 'ar' => 'لا يمكن إرسال القيد في الحالة الحالية.'],
            'accounting.cannot_approve' => ['en' => 'Entry cannot be approved in current status.', 'ar' => 'لا يمكن الموافقة على القيد في الحالة الحالية.'],
            'accounting.cannot_reject' => ['en' => 'Entry cannot be rejected in current status.', 'ar' => 'لا يمكن رفض القيد في الحالة الحالية.'],
            'accounting.cannot_post' => ['en' => 'Entry must be approved before posting.', 'ar' => 'يجب الموافقة على القيد قبل التسجيل.'],
            'accounting.already_posted' => ['en' => 'Entry is already posted.', 'ar' => 'القيد مسجل بالفعل.'],

            // Status
            'accounting.status.draft' => ['en' => 'Draft', 'ar' => 'مسودة'],
            'accounting.status.pending_approval' => ['en' => 'Pending Approval', 'ar' => 'في انتظار الموافقة'],
            'accounting.status.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'accounting.status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'accounting.status.posted' => ['en' => 'Posted', 'ar' => 'مسجل'],

            // Validation
            'accounting.validation.account_required' => ['en' => 'Account is required', 'ar' => 'الحساب مطلوب'],
            'accounting.validation.debit_or_credit_required' => ['en' => 'Either debit or credit must be entered, not both', 'ar' => 'يجب إدخال إما مدين أو دائن، وليس كلاهما'],
            'accounting.validation.amount_required' => ['en' => 'Debit or credit amount is required', 'ar' => 'مبلغ المدين أو الدائن مطلوب'],
            'accounting.validation.exchange_rate_required' => ['en' => 'Exchange rate is required for foreign currency', 'ar' => 'سعر الصرف مطلوب للعملة الأجنبية'],
            'accounting.validation.journal_required' => ['en' => 'Journal is required', 'ar' => 'اليومية مطلوبة'],
            'accounting.validation.entry_date_required' => ['en' => 'Entry date is required', 'ar' => 'تاريخ القيد مطلوب'],
            'accounting.validation.branch_required' => ['en' => 'Branch is required', 'ar' => 'الفرع مطلوب'],
            'accounting.validation.lines_required' => ['en' => 'At least one journal entry line is required', 'ar' => 'يجب أن يكون هناك بند قيد واحد على الأقل'],
            'accounting.validation.minimum_two_lines' => ['en' => 'At least two journal entry lines are required', 'ar' => 'يجب أن يكون هناك بندان قيد على الأقل'],
            'accounting.validation.line_error' => ['en' => 'Line :index: :error', 'ar' => 'البند :index: :error'],
            'accounting.validation.period_closed' => ['en' => 'Cannot post to a closed period.', 'ar' => 'لا يمكن التسجيل في فترة مغلقة.'],
            'accounting.validation.cannot_edit_posted' => ['en' => 'Cannot edit a posted entry. Create a reversal entry instead.', 'ar' => 'لا يمكن تعديل قيد مسجل. قم بإنشاء قيد عكسي بدلاً من ذلك.'],
            'accounting.validation.account_not_allowed' => ['en' => 'Account is not active or does not allow manual entry.', 'ar' => 'الحساب غير نشط أو لا يسمح بالإدخال اليدوي.'],
            'accounting.validation.debit_or_credit' => ['en' => 'Line must have either debit or credit, not both.', 'ar' => 'يجب أن يكون للبند إما مدين أو دائن، وليس كلاهما.'],
            'accounting.validation.cannot_edit_status' => ['en' => 'Cannot edit entry in current status.', 'ar' => 'لا يمكن تعديل القيد في الحالة الحالية.'],

            // Auto-generated
            'accounting.auto_generated' => ['en' => 'Auto-generated entry number', 'ar' => 'رقم القيد المولد تلقائياً'],
            'accounting.external_reference' => ['en' => 'External reference number (optional)', 'ar' => 'رقم المرجع الخارجي (اختياري)'],

            // Signature
            'accounting.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'accounting.title' => ['en' => 'Title', 'ar' => 'المسمى الوظيفي'],
            'accounting.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
        ];

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

        $this->command->info('Accounting translations seeded successfully!');
    }
}

