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
        $english = Language::where('code', 'en')->first();
        $arabic  = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Finance translations.');
            return;
        }

        $this->command->info('Creating Finance module translations...');

        $translations = [
            // =========================
            // Navigation Group
            // =========================
            'navigation.groups.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'sidebar.finance'           => ['en' => 'Finance', 'ar' => 'المالية'],

            // Navigation Items
            'navigation.finance_branch_transactions' => ['en' => 'Income & Expenses', 'ar' => 'الإيرادات والمصروفات'],
            'sidebar.finance.branch_transactions'    => ['en' => 'Income & Expenses', 'ar' => 'الإيرادات والمصروفات'],

            // =========================
            // Forms: Branch Transactions
            // =========================
            'forms.branch_tx.sections.main'        => ['en' => 'Document Details', 'ar' => 'بيانات المستند'],
            'forms.branch_tx.transaction_date'     => ['en' => 'Transaction Date', 'ar' => 'تاريخ العملية'],
            'forms.branch_tx.type'                 => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.branch_tx.type_income'          => ['en' => 'Income', 'ar' => 'إيراد'],
            'forms.branch_tx.type_expense'         => ['en' => 'Expense', 'ar' => 'مصروف'],
            'forms.branch_tx.branch'               => ['en' => 'Branch', 'ar' => 'الفرع'],
            'forms.branch_tx.country'              => ['en' => 'Country', 'ar' => 'الدولة'],
            'forms.branch_tx.amount'               => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'forms.branch_tx.currency'             => ['en' => 'Currency', 'ar' => 'العملة'],
            'forms.branch_tx.receiver_name'        => ['en' => 'Receiver/Beneficiary Name', 'ar' => 'اسم المستلم/المستفيد'],
            'forms.branch_tx.payment_method'       => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'forms.branch_tx.reference_no'         => ['en' => 'Reference No.', 'ar' => 'رقم المرجع'],
            'forms.branch_tx.notes'                => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'forms.branch_tx.attachment'           => ['en' => 'Attachment', 'ar' => 'مرفق'],
            'forms.branch_tx.approval_note'        => ['en' => 'Approval Note', 'ar' => 'ملاحظة الموافقة'],
            'forms.branch_tx.rejection_note'       => ['en' => 'Rejection Note', 'ar' => 'ملاحظة الرفض'],

            // Helpers (اختياري)
            'forms.branch_tx.attachment.helper'    => ['en' => 'Upload image or PDF (optional)', 'ar' => 'ارفع صورة أو PDF (اختياري)'],
            'forms.branch_tx.document_no.helper'   => ['en' => 'Auto-generated document number', 'ar' => 'رقم مستند يتم توليده تلقائيًا'],

            // =========================
            // Tables: Branch Transactions
            // =========================
            'tables.branch_tx.document_no'         => ['en' => 'Document No.', 'ar' => 'رقم المستند'],
            'tables.branch_tx.branch'              => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.branch_tx.country'             => ['en' => 'Country', 'ar' => 'الدولة'],
            'tables.branch_tx.type'                => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.branch_tx.amount'              => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'tables.branch_tx.amount_base'         => ['en' => 'Base Amount', 'ar' => 'بالعملة الأساسية'],
            'tables.branch_tx.status'              => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.branch_tx.transaction_date'    => ['en' => 'Date', 'ar' => 'التاريخ'],
            'tables.branch_tx.receiver_name'       => ['en' => 'Receiver', 'ar' => 'المستلم'],

            // Status labels
            'tables.branch_tx.status_pending'      => ['en' => 'Pending', 'ar' => 'قيد المراجعة'],
            'tables.branch_tx.status_approved'     => ['en' => 'Approved', 'ar' => 'مقبول'],
            'tables.branch_tx.status_rejected'     => ['en' => 'Rejected', 'ar' => 'مرفوض'],

            // Filters
            'tables.branch_tx.filters.branch'      => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.branch_tx.filters.country'     => ['en' => 'Country', 'ar' => 'الدولة'],
            'tables.branch_tx.filters.currency'    => ['en' => 'Currency', 'ar' => 'العملة'],
            'tables.branch_tx.filters.type'        => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.branch_tx.filters.status'      => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.branch_tx.filters.date_range'  => ['en' => 'Date Range', 'ar' => 'نطاق التاريخ'],

            // =========================
            // Actions (لو موجودة عندك عامة already سيبها)
            // =========================
            'actions.approve'                      => ['en' => 'Approve', 'ar' => 'موافقة'],
            'actions.reject'                       => ['en' => 'Reject', 'ar' => 'رفض'],
            'actions.print'                        => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.export_excel'                 => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf'                   => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],

            // =========================
            // Reports
            // =========================
            'reports.branch_tx.title'              => ['en' => 'Income & Expenses Report', 'ar' => 'تقرير الإيرادات والمصروفات'],

            // =========================
            // Print
            // =========================
            'print.branch_tx.title'                => ['en' => 'Income/Expense Document', 'ar' => 'مستند إيراد/مصروف'],
            'print.branch_tx.created_at'           => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],
            'print.branch_tx.created_by'           => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],
            'print.branch_tx.approved_by'          => ['en' => 'Approved By', 'ar' => 'تمت الموافقة بواسطة'],
            'print.branch_tx.rejected_by'          => ['en' => 'Rejected By', 'ar' => 'تم الرفض بواسطة'],
        ];

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

        $this->command->info("✓ Finance translations created: {$created} entries");
    }
}
