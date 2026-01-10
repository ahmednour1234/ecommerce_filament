<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HrLoansTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating HR Loans translations...');

        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->error('English or Arabic language not found. Please seed languages first.');
            return;
        }

        $translations = [
            'navigation.hr_loans' => ['en' => 'Loans', 'ar' => 'القروض'],
            'navigation.hr_loan_types' => ['en' => 'Loan Types', 'ar' => 'أنواع القروض'],
            'fields.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'fields.loan_type' => ['en' => 'Loan Type', 'ar' => 'نوع القرض'],
            'fields.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'fields.installments' => ['en' => 'Installments', 'ar' => 'الأقساط'],
            'fields.max_installments' => ['en' => 'Max Installments', 'ar' => 'أقصى عدد أقساط'],
            'fields.max_amount' => ['en' => 'Max Amount', 'ar' => 'أقصى مبلغ'],
            'fields.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'fields.exchange_rate' => ['en' => 'Exchange Rate', 'ar' => 'سعر الصرف'],
            'fields.base_amount' => ['en' => 'Base Amount', 'ar' => 'المبلغ الأساسي'],
            'fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البدء'],
            'fields.purpose' => ['en' => 'Purpose', 'ar' => 'الغرض'],
            'fields.attachment' => ['en' => 'Attachment', 'ar' => 'المرفق'],
            'fields.installment_amount' => ['en' => 'Installment Amount', 'ar' => 'مبلغ القسط'],
            'fields.due_date' => ['en' => 'Due Date', 'ar' => 'تاريخ الاستحقاق'],
            'fields.paid_at' => ['en' => 'Paid At', 'ar' => 'تاريخ الدفع'],
            'fields.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'fields.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'fields.description_ar' => ['en' => 'Description (Arabic)', 'ar' => 'الوصف (عربي)'],
            'fields.description_en' => ['en' => 'Description (English)', 'ar' => 'الوصف (إنجليزي)'],
            'fields.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'fields.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'fields.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'actions.calculate' => ['en' => 'Calculate Loan', 'ar' => 'حساب القرض'],
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.mark_paid' => ['en' => 'Mark as Paid', 'ar' => 'تمييز كمدفوع'],
            'actions.close' => ['en' => 'Close', 'ar' => 'إغلاق'],
            'status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'status.closed' => ['en' => 'Closed', 'ar' => 'مغلق'],
            'status.pending' => ['en' => 'Pending', 'ar' => 'معلق'],
            'status.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'status.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],
            'stats.total_loans' => ['en' => 'Total Loans', 'ar' => 'إجمالي القروض'],
            'stats.active_loans' => ['en' => 'Active Loans', 'ar' => 'القروض النشطة'],
            'stats.closed_loans' => ['en' => 'Closed Loans', 'ar' => 'القروض المغلقة'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
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

            $created++;
        }

        $this->command->info("✓ HR Loans translations created/updated: {$created} keys");
    }
}
