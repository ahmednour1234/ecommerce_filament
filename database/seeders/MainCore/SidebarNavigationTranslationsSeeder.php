<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class SidebarNavigationTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping sidebar navigation translations.');
            return;
        }

        $this->command->info('Creating sidebar navigation translations...');

        $translations = [
            // Navigation Groups (with sidebar prefix)
            'sidebar.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.housing' => ['en' => 'Housing', 'ar' => 'الإيواء'],
            'sidebar.rental' => ['en' => 'Rental', 'ar' => 'قسم التأجير'],
            'sidebar.service_transfer' => ['en' => 'Service Transfer', 'ar' => 'نقل الخدمات'],
            'sidebar.packages' => ['en' => 'Packages', 'ar' => 'باقات العروض'],
            'sidebar.candidates' => ['en' => 'Candidates', 'ar' => 'المرشحين'],
            'sidebar.clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'sidebar.agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'sidebar.finance' => ['en' => 'Finance', 'ar' => 'قسم الحسابات'],
            'sidebar.follow_up' => ['en' => 'Follow-up', 'ar' => 'المتابعة'],
            'sidebar.messages' => ['en' => 'Messages', 'ar' => 'قسم الرسائل'],
            'sidebar.company_visas' => ['en' => 'Company Visas', 'ar' => 'تأشيرات الشركة'],
            'sidebar.app_management' => ['en' => 'App Management', 'ar' => 'إدارة التطبيق'],
            'sidebar.profile' => ['en' => 'Profile', 'ar' => 'الملف الشخصي'],
            'sidebar.employee_commissions' => ['en' => 'Employee Commissions', 'ar' => 'عمولات الموظفين'],
            'sidebar.hr' => ['en' => 'Human Resources', 'ar' => 'الموارد البشرية'],
            'sidebar.system_movement' => ['en' => 'System Movement', 'ar' => 'حركة النظام المرجعي'],
            'sidebar.notifications' => ['en' => 'Notifications', 'ar' => 'التنبيهات'],
            'sidebar.settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'sidebar.branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'sidebar.website_management' => ['en' => 'Website Management', 'ar' => 'إدارة الموقع'],

            // Navigation Groups (direct keys - for Filament group translation)
            'recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'housing' => ['en' => 'Housing', 'ar' => 'الإيواء'],
            'rental' => ['en' => 'Rental', 'ar' => 'قسم التأجير'],
            'service_transfer' => ['en' => 'Service Transfer', 'ar' => 'نقل الخدمات'],
            'packages' => ['en' => 'Packages', 'ar' => 'باقات العروض'],
            'candidates' => ['en' => 'Candidates', 'ar' => 'المرشحين'],
            'clients' => ['en' => 'Clients', 'ar' => 'العملاء'],
            'agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'finance' => ['en' => 'Finance', 'ar' => 'قسم الحسابات'],
            'follow_up' => ['en' => 'Follow-up', 'ar' => 'المتابعة'],
            'messages' => ['en' => 'Messages', 'ar' => 'قسم الرسائل'],
            'company_visas' => ['en' => 'Company Visas', 'ar' => 'تأشيرات الشركة'],
            'app_management' => ['en' => 'App Management', 'ar' => 'إدارة التطبيق'],
            'profile' => ['en' => 'Profile', 'ar' => 'الملف الشخصي'],
            'employee_commissions' => ['en' => 'Employee Commissions', 'ar' => 'عمولات الموظفين'],
            'hr' => ['en' => 'Human Resources', 'ar' => 'الموارد البشرية'],
            'system_movement' => ['en' => 'System Movement', 'ar' => 'حركة النظام المرجعي'],
            'notifications' => ['en' => 'Notifications', 'ar' => 'التنبيهات'],
            'settings' => ['en' => 'Settings', 'ar' => 'الإعدادات'],
            'branches' => ['en' => 'Branches', 'ar' => 'الفروع'],
            'website_management' => ['en' => 'Website Management', 'ar' => 'إدارة الموقع'],

            // Recruitment Contracts
            'sidebar.recruitment_contracts.recruitmentcontract' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.recruitment_contracts.receivingrecruitmentreport' => ['en' => 'Receiving Labor', 'ar' => 'استلام العمالة'],
            'sidebar.recruitment_contracts.received_workers' => ['en' => 'Received Workers', 'ar' => 'العمالة المستلمة'],
            'sidebar.recruitment_contracts.expired_contracts' => ['en' => 'Expired Contracts', 'ar' => 'العقود المنتهية'],
            'sidebar.recruitment_contracts.contracts_reports' => ['en' => 'Recruitment Contracts Reports', 'ar' => 'تقارير عقود الاستقدام'],

            // Housing
            'sidebar.housing.status_management' => ['en' => 'Status Management', 'ar' => 'إدارة الحالات'],
            'sidebar.housing.buildings_management' => ['en' => 'Buildings Management', 'ar' => 'إدارة المباني'],
            'sidebar.housing.housing_requests' => ['en' => 'Housing Requests', 'ar' => 'طلبات الإيواء'],
            'sidebar.housing.accommodation_entries' => ['en' => 'Accommodation Entries', 'ar' => 'إدخالات الإيواء'],

            // Rental
            'sidebar.rental.rentalcontract' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'sidebar.rental.rentalcontractrequest' => ['en' => 'Rental Requests', 'ar' => 'طلبات التأجير'],
            'sidebar.rental.rentalcancelrefundrequest' => ['en' => 'Cancel/Refund Requests', 'ar' => 'طلبات الإلغاء/الاسترجاع'],
            'sidebar.rental.returned_contracts' => ['en' => 'Returned Contracts', 'ar' => 'العقود المسترجعة'],
            'sidebar.rental.archived_contracts' => ['en' => 'Archived Contracts', 'ar' => 'العقود المؤرشفة'],
            'sidebar.rental.reports' => ['en' => 'Rental Reports', 'ar' => 'تقارير التأجير'],

            // Service Transfer
            'sidebar.servicetransfer' => ['en' => 'Service Transfer Requests', 'ar' => 'طلبات نقل الخدمات'],
            'sidebar.servicetransferrequestsreport' => ['en' => 'Service Transfer Requests Report', 'ar' => 'تقرير طلبات نقل الخدمات'],
            'sidebar.servicetransferpaymentsreport' => ['en' => 'Service Transfer Payments Report', 'ar' => 'تقرير المدفوعات – نقل الخدمات'],

            // Packages
            'sidebar.packages.package' => ['en' => 'Packages', 'ar' => 'الباقات'],

            // Candidates
            'sidebar.candidates.laborer' => ['en' => 'Laborers', 'ar' => 'العمال'],
            'sidebar.candidates.nationality' => ['en' => 'Nationalities', 'ar' => 'الجنسيات'],
            'sidebar.candidates.profession' => ['en' => 'Professions', 'ar' => 'المهن'],

            // Clients
            'sidebar.clients.client' => ['en' => 'Clients', 'ar' => 'العملاء'],

            // Agents
            'sidebar.recruitment.agents' => ['en' => 'Agents', 'ar' => 'الوكلاء'],
            'sidebar.agents.agentlaborprice' => ['en' => 'Agent Labor Prices', 'ar' => 'أسعار عمل الوكلاء'],

            // Finance
            'sidebar.finance.branchtransaction' => ['en' => 'Income & Expenses', 'ar' => 'الإيرادات والمصروفات'],
            'sidebar.finance.types' => ['en' => 'Finance Types', 'ar' => 'أنواع المالية'],
            'sidebar.finance.branch_statement' => ['en' => 'Branch Statement', 'ar' => 'كشف حساب الفرع'],
            'sidebar.finance.income_statement_by_branch' => ['en' => 'Income Statement by Branch', 'ar' => 'قائمة الدخل حسب الفرع'],
            'sidebar.finance.income_report' => ['en' => 'Income Report', 'ar' => 'تقرير الإيرادات'],
            'sidebar.finance.expense_report' => ['en' => 'Expense Report', 'ar' => 'تقرير المصروفات'],
            'sidebar.finance.importbranchtransactions' => ['en' => 'Import from Excel', 'ar' => 'استيراد من Excel'],

            // Follow-up
            'sidebar.complaints.complaints' => ['en' => 'Complaints Department', 'ar' => 'قسم الشكاوي'],

            // Company Visas
            'sidebar.company_visas.companyvisarequest' => ['en' => 'Visa Requests', 'ar' => 'طلبات التأشيرات'],
            'sidebar.company_visas.companyvisacontract' => ['en' => 'Company Recruitment Contracts', 'ar' => 'عقود استقدام الشركة'],

            // App Management
            'sidebar.general_settings.app_settings' => ['en' => 'App Settings', 'ar' => 'إعدادات التطبيق'],
            'sidebar.general_settings.maincore' => ['en' => 'Main Core', 'ar' => 'النواة الرئيسية'],

            // HR Settings
            'sidebar.hr.settings.departments' => ['en' => 'Departments', 'ar' => 'الإدارات'],
            'sidebar.hr.settings.positions' => ['en' => 'Positions', 'ar' => 'المسميات الوظيفية'],
            'sidebar.hr.settings.identity_types' => ['en' => 'Identity Types', 'ar' => 'نوع الهوية'],
            'sidebar.hr.settings.blood_types' => ['en' => 'Blood Types', 'ar' => 'فصائل الدم'],
            'sidebar.hr.settings.banks' => ['en' => 'Banks', 'ar' => 'البنوك'],
            'sidebar.hr.settings.work_places' => ['en' => 'Work Places', 'ar' => 'أماكن العمل'],
            'sidebar.hr.settings.assign_work_places' => ['en' => 'Assign Work Places', 'ar' => 'تعيين أماكن العمل'],
            'sidebar.hr.settings.leave_types' => ['en' => 'Leave Types', 'ar' => 'أنواع الإجازات'],
            'sidebar.hr.settings.holidays' => ['en' => 'Holidays', 'ar' => 'العطلات الرسمية'],
            'sidebar.hr.settings.holidays_calendar' => ['en' => 'Holidays Calendar', 'ar' => 'تقويم العطلات'],
            'sidebar.hr.settings.loan_types' => ['en' => 'Loan Types', 'ar' => 'أنواع القروض'],
            'sidebar.hr.settings.salary_components' => ['en' => 'Salary Components', 'ar' => 'المكونات المالية'],

            // HR Employees
            'sidebar.hr.employees.employee_groups' => ['en' => 'Employee Groups', 'ar' => 'مجموعات الموظفين'],
            'sidebar.hr.employees.employee_financial' => ['en' => 'Employee Financial Profiles', 'ar' => 'البيانات المالية للموظفين'],
            'sidebar.hr.attendance.devices' => ['en' => 'Devices', 'ar' => 'أجهزة البصمة'],
            'sidebar.hr.attendance.biometric_attendance' => ['en' => 'Biometric Attendance', 'ar' => 'سجلات الحضور الحيوية'],
            'sidebar.hr.attendance.work_schedules' => ['en' => 'Work Schedules', 'ar' => 'مواعيد العمل'],
            'sidebar.hr.attendance.copy_schedules' => ['en' => 'Copy Schedules', 'ar' => 'نسخ المواعيد'],
            'sidebar.hr.attendance.daily_attendance' => ['en' => 'Daily Attendance', 'ar' => 'الحضور اليومي'],
            'sidebar.hr.attendance.monthly_attendance_calendar' => ['en' => 'Monthly Attendance Calendar', 'ar' => 'تقرير الحضور الشهري'],
            'sidebar.hr.attendance.monthly_attendance_report' => ['en' => 'Monthly Attendance Report', 'ar' => 'تقرير الحضور الشهري'],
            'sidebar.hr.leaves_holidays.leave_balance' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'sidebar.hr.leaves_holidays.leave_requests' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'sidebar.hr.leaves_holidays.leave_report' => ['en' => 'Leave Report', 'ar' => 'تقرير الإجازات'],
            'sidebar.hr.loans.loans' => ['en' => 'Loans', 'ar' => 'القروض'],
            'sidebar.hr.payroll.payroll' => ['en' => 'Payroll', 'ar' => 'الرواتب'],
            'sidebar.hr.requests.excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
            'sidebar.hr.employees.employees' => ['en' => 'Employees', 'ar' => 'الموظفين'],

            // System Movement
            'sidebar.system_movement.user' => ['en' => 'Users', 'ar' => 'المستخدمون'],
            'sidebar.system_movement.role' => ['en' => 'Roles', 'ar' => 'الأدوار'],
            'sidebar.system_movement.permission' => ['en' => 'Permissions', 'ar' => 'الصلاحيات'],

            // Notifications
            'sidebar.recruitment_contracts.contract_alerts' => ['en' => 'Contract Alerts', 'ar' => 'تنبيهات العقود'],

            // Settings
            'sidebar.general_settings.translations' => ['en' => 'Translations', 'ar' => 'الترجمات'],
            'sidebar.general_settings.languages' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'sidebar.general_settings.currencies' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'sidebar.general_settings.currency_rates' => ['en' => 'Currency Rates', 'ar' => 'أسعار العملات'],
            'sidebar.settings.translation' => ['en' => 'Translations', 'ar' => 'الترجمات'],
            'sidebar.settings.language' => ['en' => 'Languages', 'ar' => 'اللغات'],
            'sidebar.settings.currency' => ['en' => 'Currencies', 'ar' => 'العملات'],
            'sidebar.settings.currencyrate' => ['en' => 'Currency Rates', 'ar' => 'أسعار العملات'],
            'sidebar.settings.settin' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],

            // Branches
            'sidebar.branches.branch' => ['en' => 'Branches', 'ar' => 'الفروع'],
        ];

        $created = 0;
        $updated = 0;

        // Group keys that should also be added to 'navigation' group for Filament compatibility
        $navigationGroupKeys = [
            'recruitment_contracts', 'housing', 'rental', 'service_transfer', 'packages',
            'candidates', 'clients', 'agents', 'finance', 'follow_up', 'messages',
            'company_visas', 'app_management', 'profile', 'employee_commissions',
            'hr', 'system_movement', 'notifications', 'settings', 'branches', 'website_management'
        ];

        foreach ($translations as $key => $values) {
            // Save to 'dashboard' group (primary)
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

            // Also add direct group keys to 'navigation' group for Filament compatibility
            if (in_array($key, $navigationGroupKeys)) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'navigation',
                        'language_id' => $english->id,
                    ],
                    [
                        'value' => $values['en'],
                    ]
                );

                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'navigation',
                        'language_id' => $arabic->id,
                    ],
                    [
                        'value' => $values['ar'],
                    ]
                );

                // Also add to 'filament' group for Filament's default translation system
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'filament',
                        'language_id' => $english->id,
                    ],
                    [
                        'value' => $values['en'],
                    ]
                );

                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'filament',
                        'language_id' => $arabic->id,
                    ],
                    [
                        'value' => $values['ar'],
                    ]
                );
            }
        }

        $this->command->info("✓ Sidebar navigation translations created: {$created}, updated: {$updated}");
    }
}
