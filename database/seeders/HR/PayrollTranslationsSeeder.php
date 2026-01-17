<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class PayrollTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping payroll translations.');
            return;
        }

        $translations = [
            'sidebar.hr.payroll' => ['en' => 'Payroll', 'ar' => 'الرواتب'],
            'sidebar.hr.salary_components' => ['en' => 'Salary Components', 'ar' => 'المكونات المالية'],
            'sidebar.hr.employee_financial_profiles' => ['en' => 'Employee Financial Profiles', 'ar' => 'البيانات المالية للموظفين'],

            'tables.salary_components.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.salary_components.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.salary_components.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.salary_components.is_fixed' => ['en' => 'Fixed', 'ar' => 'ثابت'],
            'tables.salary_components.taxable' => ['en' => 'Taxable', 'ar' => 'خاضع للضريبة'],
            'tables.salary_components.default_amount' => ['en' => 'Default Amount', 'ar' => 'المبلغ الافتراضي'],
            'tables.salary_components.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.salary_components.earnings' => ['en' => 'Earnings', 'ar' => 'المستحقات'],
            'tables.salary_components.deductions' => ['en' => 'Deductions', 'ar' => 'الاستقطاعات'],

            'tables.hr_payroll.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_payroll.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_payroll.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_payroll.period' => ['en' => 'Period', 'ar' => 'الفترة'],
            'tables.hr_payroll.basic_salary' => ['en' => 'Basic Salary', 'ar' => 'الراتب الأساسي'],
            'tables.hr_payroll.total_earnings' => ['en' => 'Total Earnings', 'ar' => 'إجمالي المستحقات'],
            'tables.hr_payroll.total_deductions' => ['en' => 'Total Deductions', 'ar' => 'إجمالي الاستقطاعات'],
            'tables.hr_payroll.net_salary' => ['en' => 'Net Salary', 'ar' => 'صافي الراتب'],
            'tables.hr_payroll.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_payroll.filters.month' => ['en' => 'Month', 'ar' => 'الشهر'],
            'tables.hr_payroll.filters.year' => ['en' => 'Year', 'ar' => 'السنة'],
            'tables.hr_payroll.filters.department' => ['en' => 'Department', 'ar' => 'القسم'],

            'tables.employee_financial_profiles.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'tables.employee_financial_profiles.base_salary' => ['en' => 'Base Salary', 'ar' => 'الراتب الأساسي'],
            'tables.employee_financial_profiles.total_earnings' => ['en' => 'Total Earnings', 'ar' => 'إجمالي المستحقات'],
            'tables.employee_financial_profiles.total_deductions' => ['en' => 'Total Deductions', 'ar' => 'إجمالي الاستقطاعات'],
            'tables.employee_financial_profiles.status' => ['en' => 'Status', 'ar' => 'الحالة'],

            'forms.salary_components.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.salary_components.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.salary_components.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'forms.salary_components.type.earning' => ['en' => 'Earning', 'ar' => 'مستحق'],
            'forms.salary_components.type.deduction' => ['en' => 'Deduction', 'ar' => 'استقطاع'],
            'forms.salary_components.is_fixed' => ['en' => 'Fixed Amount', 'ar' => 'مبلغ ثابت'],
            'forms.salary_components.taxable' => ['en' => 'Taxable', 'ar' => 'خاضع للضريبة'],
            'forms.salary_components.default_amount' => ['en' => 'Default Amount', 'ar' => 'المبلغ الافتراضي'],
            'forms.salary_components.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.salary_components.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            'forms.payroll.month' => ['en' => 'Month', 'ar' => 'الشهر'],
            'forms.payroll.year' => ['en' => 'Year', 'ar' => 'السنة'],
            'forms.payroll.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'forms.payroll.include_attendance_deductions' => ['en' => 'Include Attendance Deductions', 'ar' => 'تضمين استقطاعات الحضور'],
            'forms.payroll.include_loan_installments' => ['en' => 'Include Loan Installments', 'ar' => 'تضمين أقساط القروض'],
            'forms.payroll.create_payroll' => ['en' => 'Create Payroll Sheet', 'ar' => 'إنشاء كشوف المرتبات'],

            'forms.employee_financial_profiles.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'forms.employee_financial_profiles.base_salary' => ['en' => 'Base Salary', 'ar' => 'الراتب الأساسي'],
            'forms.employee_financial_profiles.currency' => ['en' => 'Currency', 'ar' => 'العملة'],
            'forms.employee_financial_profiles.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'forms.employee_financial_profiles.status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'forms.employee_financial_profiles.status.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],
            'forms.employee_financial_profiles.joined_at' => ['en' => 'Joined Date', 'ar' => 'تاريخ الانضمام'],
            'forms.employee_financial_profiles.earnings' => ['en' => 'Earnings', 'ar' => 'المستحقات'],
            'forms.employee_financial_profiles.deductions' => ['en' => 'Deductions', 'ar' => 'الاستقطاعات'],
            'forms.employee_financial_profiles.component' => ['en' => 'Component', 'ar' => 'المكون'],
            'forms.employee_financial_profiles.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'forms.employee_financial_profiles.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],

            'actions.approve_all' => ['en' => 'Approve All', 'ar' => 'الموافقة على الكل'],
            'actions.pay_all' => ['en' => 'Pay All', 'ar' => 'دفع الكل'],
            'actions.print_payslip' => ['en' => 'Print Payslip', 'ar' => 'طباعة مسير رواتب'],
            'actions.print_payroll' => ['en' => 'Print Payroll Sheet', 'ar' => 'طباعة مسير رواتب'],
            'actions.export_excel' => ['en' => 'Export Excel', 'ar' => 'تصدير Excel'],
            'actions.export_pdf' => ['en' => 'Export PDF', 'ar' => 'تصدير PDF'],
            'actions.edit_financial_data' => ['en' => 'Edit Financial Data', 'ar' => 'تعديل البيانات المالية'],

            'status.draft' => ['en' => 'Draft', 'ar' => 'مسودة'],
            'status.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'status.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            $resultEn = Translation::updateOrCreate(
                ['key' => $key, 'group' => 'dashboard', 'language_id' => $english->id],
                ['value' => $values['en']]
            );

            $resultAr = Translation::updateOrCreate(
                ['key' => $key, 'group' => 'dashboard', 'language_id' => $arabic->id],
                ['value' => $values['ar']]
            );

            if ($resultEn->wasRecentlyCreated || $resultAr->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Payroll translations seeded: {$created} created, {$updated} updated.");
    }
}
