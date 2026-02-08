<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class NavigationRefactorTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping navigation translations.');
            return;
        }

        $translations = [
            // Groups
            'sidebar.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.general_settings' => ['en' => 'General Settings', 'ar' => 'الإعدادات العامة'],
            'sidebar.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'sidebar.finance' => ['en' => 'Finance', 'ar' => 'المالية'],
            'sidebar.recruitment' => ['en' => 'Recruitment', 'ar' => 'التوظيف'],
            'sidebar.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.rental' => ['en' => 'Rental', 'ar' => 'قسم التأجير'],
            'sidebar.system' => ['en' => 'System', 'ar' => 'النظام'],
            'sidebar.hr' => ['en' => 'Human Resources', 'ar' => 'الموارد البشرية'],

            // General Settings
            'sidebar.general_settings.main_settings' => ['en' => 'Main Settings', 'ar' => 'الإعدادات الرئيسية'],
            'sidebar.general_settings.translations' => ['en' => 'Translations', 'ar' => 'الترجمات'],
            'sidebar.general_settings.branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'sidebar.general_settings.languages' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'sidebar.general_settings.currencies' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'sidebar.general_settings.currency_rates' => ['en' => 'Currency Rates', 'ar' => 'أسعار العملات'],
            'sidebar.general_settings.app_settings' => ['en' => 'App Settings', 'ar' => 'إعدادات التطبيق'],
            'sidebar.general_settings.maincore' => ['en' => 'MainCore', 'ar' => 'النواة الرئيسية'],
            'sidebar.general_settings.system_settings' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],

            // Finance
            'sidebar.finance.settings' => ['en' => 'Finance Settings', 'ar' => 'إعدادات المالية'],
            'sidebar.finance.types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],
            'sidebar.finance.income_expenses' => ['en' => 'Income & Expenses', 'ar' => 'الايرادات والمصروفات'],
            'sidebar.finance.reports' => ['en' => 'Financial Reports', 'ar' => 'تقارير مالية'],
            'sidebar.finance.branch_statement' => ['en' => 'Branch Account Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.income_statement_by_branch' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
            'sidebar.finance.income_report' => ['en' => 'Income Report', 'ar' => 'تقرير الإيرادات'],
            'sidebar.finance.expense_report' => ['en' => 'Expense Report', 'ar' => 'تقرير المصروفات'],
            'sidebar.finance.tools' => ['en' => 'Tools', 'ar' => 'أدوات'],
            'sidebar.finance.import_excel' => ['en' => 'Import from Excel', 'ar' => 'استيراد من Excel'],

            // Recruitment
            'sidebar.recruitment.settings' => ['en' => 'Recruitment Settings', 'ar' => 'إعدادات الاستقدام'],
            'sidebar.recruitment.agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'sidebar.recruitment.agent_labor_prices' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],
            'sidebar.recruitment.nationalities' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'sidebar.recruitment.professions' => ['en' => 'Professions', 'ar' => 'المهن'],
            'sidebar.recruitment.laborers' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'sidebar.recruitment.used_laborers' => ['en' => 'Used Laborers', 'ar' => 'العمال المستخدمون'],
            'sidebar.recruitment.packages' => ['en' => 'Packages', 'ar' => 'الباقات'],
            
            // Recruitment Contracts Group
            'sidebar.recruitment_contracts.contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.recruitment_contracts.received_workers' => ['en' => 'Received Workers', 'ar' => 'العمالة المستلمة'],
            'sidebar.recruitment_contracts.receiving_labor' => ['en' => 'Receiving Labor', 'ar' => 'استلام العمالة'],
            'sidebar.recruitment_contracts.expired_contracts' => ['en' => 'Expired Contracts', 'ar' => 'العقود المنتهية'],
            'sidebar.recruitment_contracts.contracts_reports' => ['en' => 'Recruitment Contracts Reports', 'ar' => 'تقارير عقود الاستقدام'],

            // Rental
            'sidebar.rental.contracts_requests' => ['en' => 'Contracts & Requests', 'ar' => 'العقود والطلبات'],
            'sidebar.rental.contracts' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'sidebar.rental.requests' => ['en' => 'Rental Requests', 'ar' => 'طلبات التأجير'],
            'sidebar.rental.cancel_refund_requests' => ['en' => 'Cancel/Refund Requests', 'ar' => 'طلبات الإلغاء/الاسترجاع'],
            'sidebar.rental.archive' => ['en' => 'Archive', 'ar' => 'الأرشيف'],
            'sidebar.rental.returned_contracts' => ['en' => 'Returned Contracts', 'ar' => 'العقود المسترجعة'],
            'sidebar.rental.archived_contracts' => ['en' => 'Archived Contracts', 'ar' => 'العقود المؤرشفة'],
            'sidebar.rental.reports' => ['en' => 'Rental Reports', 'ar' => 'تقارير التأجير'],

            // System
            'sidebar.system.users' => ['en' => 'Users', 'ar' => 'المستخدمون'],
            'sidebar.system.roles' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'sidebar.system.permissions' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],

            // HR - Section Headers
            'sidebar.hr.settings' => ['en' => 'HR Settings', 'ar' => 'إعدادات الموارد البشرية'],
            'sidebar.hr.employees' => ['en' => 'Employee Management', 'ar' => 'إدارة الموظفين'],
            'sidebar.hr.attendance' => ['en' => 'Attendance & Time', 'ar' => 'الحضور والانصراف'],
            'sidebar.hr.leaves_holidays' => ['en' => 'Leaves & Holidays', 'ar' => 'الإجازات والعطلات'],
            'sidebar.hr.loans' => ['en' => 'Loans & Advances', 'ar' => 'القروض والسلف'],
            'sidebar.hr.payroll' => ['en' => 'Payroll & Benefits', 'ar' => 'الرواتب والمستحقات'],
            'sidebar.hr.requests' => ['en' => 'Requests', 'ar' => 'الطلبات'],

            // HR Settings
            'sidebar.hr.settings.departments' => ['en' => 'Departments', 'ar' => 'الإدارات'],
            'sidebar.hr.settings.positions' => ['en' => 'Positions', 'ar' => 'المسميات الوظيفية'],
            'sidebar.hr.settings.identity_types' => ['en' => 'Identity Types', 'ar' => 'نوع الهوية'],
            'sidebar.hr.settings.blood_types' => ['en' => 'Blood Types', 'ar' => 'فصائل الدم'],
            'sidebar.hr.settings.banks' => ['en' => 'Banks', 'ar' => 'البنوك'],
            'sidebar.hr.settings.work_places' => ['en' => 'Work Places', 'ar' => 'أماكن العمل'],
            'sidebar.hr.settings.assign_work_places' => ['en' => 'Assign Work Places', 'ar' => 'تعيين أماكن العمل'],
            'sidebar.hr.settings.leave_types' => ['en' => 'Leave Types', 'ar' => 'أنواع الإجازات'],
            'sidebar.hr.settings.holidays' => ['en' => 'Official Holidays', 'ar' => 'العطلات الرسمية'],
            'sidebar.hr.settings.holidays_calendar' => ['en' => 'Holidays Calendar', 'ar' => 'تقويم العطلات'],
            'sidebar.hr.settings.loan_types' => ['en' => 'Loan Types', 'ar' => 'أنواع القروض'],
            'sidebar.hr.settings.salary_components' => ['en' => 'Salary Components', 'ar' => 'المكونات المالية'],

            // HR Employees
            'sidebar.hr.employees.employees' => ['en' => 'Employees', 'ar' => 'الموظفين'],
            'sidebar.hr.employees.employee_groups' => ['en' => 'Employee Groups', 'ar' => 'مجموعات الموظفين'],
            'sidebar.hr.employees.employee_financial' => ['en' => 'Employee Financial Data', 'ar' => 'البيانات المالية للموظفين'],

            // HR Attendance
            'sidebar.hr.attendance.devices' => ['en' => 'Biometric Devices', 'ar' => 'أجهزة البصمة'],
            'sidebar.hr.attendance.biometric_attendance' => ['en' => 'Biometric Attendance Records', 'ar' => 'سجلات الحضور الحيوية'],
            'sidebar.hr.attendance.work_schedules' => ['en' => 'Work Schedules', 'ar' => 'مواعيد العمل'],
            'sidebar.hr.attendance.copy_schedules' => ['en' => 'Copy Schedules', 'ar' => 'نسخ المواعيد'],
            'sidebar.hr.attendance.daily_attendance' => ['en' => 'Daily Attendance', 'ar' => 'الحضور اليومي'],
            'sidebar.hr.attendance.monthly_attendance_report' => ['en' => 'Monthly Attendance Report', 'ar' => 'تقرير الحضور الشهري'],

            // HR Leaves & Holidays
            'sidebar.hr.leaves_holidays.leave_balance' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'sidebar.hr.leaves_holidays.leave_requests' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'sidebar.hr.leaves_holidays.leave_report' => ['en' => 'Leave Report', 'ar' => 'تقرير الإجازات'],

            // HR Loans
            'sidebar.hr.loans.loans' => ['en' => 'Loans', 'ar' => 'القروض'],

            // HR Payroll
            'sidebar.hr.payroll.payroll' => ['en' => 'Payroll', 'ar' => 'الرواتب'],

            // HR Requests
            'sidebar.hr.requests.excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
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

        $this->command->info("Navigation refactor translations seeded: {$created} created, {$updated} updated.");
    }
}
