<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class SidebarNestedTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping sidebar translations.');
            return;
        }

        $translations = [
            'sidebar.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'sidebar.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'sidebar.finance.types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],
            'sidebar.finance.income_expenses' => ['en' => 'Income & Expenses', 'ar' => 'الايرادات والمصروفات'],
            'sidebar.finance.branch_statement' => ['en' => 'Branch Account Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.import_excel' => ['en' => 'Import from Excel', 'ar' => 'استيراد من Excel'],
            'sidebar.finance.income_statement_by_branch' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
            'sidebar.finance.income_report' => ['en' => 'Income Report', 'ar' => 'تقرير الإيرادات'],
            'sidebar.finance.expense_report' => ['en' => 'Expense Report', 'ar' => 'تقرير المصروفات'],
            'sidebar.employment' => ['en' => 'Employment', 'ar' => 'التوظيف'],
            'sidebar.employment.agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'sidebar.employment.agent_labor_prices' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            'sidebar.employment.nationalities' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'sidebar.employment.professions' => ['en' => 'Professions', 'ar' => 'المهن'],
            'sidebar.employment.laborers' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'sidebar.employment.used_laborers' => ['en' => 'Used Laborers', 'ar' => 'العمال المستخدمون'],
            'sidebar.employment.packages' => ['en' => 'Packages', 'ar' => 'الباقات'],
            'sidebar.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.rent_section' => ['en' => 'Rental Section', 'ar' => 'قسم التأجير'],
            'sidebar.rental.contracts' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'sidebar.rental.requests' => ['en' => 'Rental Requests', 'ar' => 'طلبات التأجير'],
            'sidebar.rental.cancel_refund_requests' => ['en' => 'Cancel/Refund Requests', 'ar' => 'طلبات الإلغاء/الاسترجاع'],
            'sidebar.rental.returned_contracts' => ['en' => 'Returned Contracts', 'ar' => 'العقود المسترجعة'],
            'sidebar.rental.archived_contracts' => ['en' => 'Archived Contracts', 'ar' => 'العقود المؤرشفة'],
            'sidebar.rental.reports' => ['en' => 'Rental Reports', 'ar' => 'تقارير التأجير'],
            'sidebar.available_workers' => ['en' => 'Available Workers', 'ar' => 'العمالة المتاحة'],
            'sidebar.system' => ['en' => 'System', 'ar' => 'النظام'],
            'sidebar.system.users' => ['en' => 'Users', 'ar' => 'المستخدمون'],
            'sidebar.system.roles' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'sidebar.system.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],
            'sidebar.hr' => ['en' => 'Human Resources', 'ar' => 'الموارد البشرية'],
            'sidebar.hr.basic_settings' => ['en' => 'Basic Settings', 'ar' => 'الإعدادات الأساسية'],
            'sidebar.hr.departments' => ['en' => 'Departments', 'ar' => 'الإدارات'],
            'sidebar.hr.positions' => ['en' => 'Positions', 'ar' => 'المسميات الوظيفية'],
            'sidebar.hr.identity_types' => ['en' => 'Identity Types', 'ar' => 'نوع الهوية'],
            'sidebar.hr.blood_types' => ['en' => 'Blood Types', 'ar' => 'فصائل الدم'],
            'sidebar.hr.banks' => ['en' => 'Banks', 'ar' => 'البنوك'],
            'sidebar.hr.work_places' => ['en' => 'Work Places', 'ar' => 'أماكن العمل'],
            'sidebar.hr.assign_work_places' => ['en' => 'Assign Work Places', 'ar' => 'تعيين أماكن العمل'],
            'sidebar.hr.devices' => ['en' => 'Devices', 'ar' => 'أجهزة البصمة'],
            'sidebar.hr.biometric_attendance' => ['en' => 'Biometric Attendance', 'ar' => 'سجلات الحضور الحيوية'],
            'sidebar.hr.employees' => ['en' => 'Employees', 'ar' => 'الموظفين'],
            'sidebar.hr.employee_groups' => ['en' => 'Employee Groups', 'ar' => 'مجموعات الموظفين'],
            'sidebar.hr.employee_financial' => ['en' => 'Employee Financial', 'ar' => 'البيانات المالية للموظفين'],
            'sidebar.hr.work_schedules' => ['en' => 'Work Schedules', 'ar' => 'مواعيد العمل'],
            'sidebar.hr.copy_schedules' => ['en' => 'Copy Schedules', 'ar' => 'نسخ المواعيد'],
            'sidebar.hr.daily_attendance' => ['en' => 'Daily Attendance', 'ar' => 'الحضور اليومي'],
            'sidebar.hr.monthly_attendance_report' => ['en' => 'Monthly Attendance Report', 'ar' => 'تقرير الحضور الشهري'],
            'sidebar.hr.leave_types' => ['en' => 'Leave Types', 'ar' => 'أنواع الإجازات'],
            'sidebar.hr.leave_balance' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'sidebar.hr.leave_requests' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'sidebar.hr.holidays' => ['en' => 'Holidays', 'ar' => 'العطلات الرسمية'],
            'sidebar.hr.holidays_calendar' => ['en' => 'Holidays Calendar', 'ar' => 'تقويم العطلات'],
            'sidebar.hr.leave_report' => ['en' => 'Leave Report', 'ar' => 'تقرير الإجازات'],
            'sidebar.hr.loan_types' => ['en' => 'Loan Types', 'ar' => 'أنواع القروض'],
            'sidebar.hr.loans' => ['en' => 'Loans', 'ar' => 'القروض'],
            'sidebar.hr.salary_components' => ['en' => 'Salary Components', 'ar' => 'المكونات المالية'],
            'sidebar.hr.payroll' => ['en' => 'Payroll', 'ar' => 'الرواتب'],
            'sidebar.hr.excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
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

        $this->command->info("Sidebar nested translations seeded: {$created} created, {$updated} updated.");
    }
}
