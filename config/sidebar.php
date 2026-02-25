<?php

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\Rental\RentalRequestsResource;
use App\Filament\Resources\Rental\RentalContractResource;
use App\Filament\Resources\Rental\CancelRefundRequestsResource;
use App\Filament\Resources\Rental\ReturnedContractsResource;
use App\Filament\Resources\Rental\ArchivedContractsResource;
use App\Filament\Resources\Recruitment\LaborerResource;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Filament\Resources\Recruitment\AgentResource;
use App\Filament\Resources\Recruitment\AgentLaborPriceResource;
use App\Filament\Resources\Recruitment\NationalityResource;
use App\Filament\Resources\Recruitment\ProfessionResource;
use App\Filament\Resources\Packages\PackageResource;

return [
    'items' => [
        [
            'title' => 'sidebar.dashboard',
            'icon' => 'heroicon-o-home',
            'url' => fn() => Dashboard::getUrl(),
            'permission' => null,
            'badge' => null,
        ],
        [
            'title' => 'sidebar.clients',
            'icon' => 'heroicon-o-users',
            'url' => fn() => ClientResource::getUrl(),
            'permission' => 'clients.view_any',
            'badge' => null,
        ],
        [
            'title' => 'sidebar.finance',
            'icon' => 'heroicon-o-banknotes',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                        [
                            'title' => 'sidebar.finance.types',
                            'icon' => 'heroicon-o-tag',
                            'url' => fn() => \App\Filament\Resources\Finance\FinanceTypeResource::getUrl(),
                            'permission' => 'finance_types.view_any',
                        ],
                        [
                            'title' => 'sidebar.finance.income_expenses',
                            'icon' => 'heroicon-o-chart-bar',
                            'url' => fn() => \App\Filament\Resources\Finance\Reports\Pages\FinanceIncomeExpenseReport::getUrl(),
                            'permission' => 'finance_reports.view',
                        ],
                        [
                            'title' => 'sidebar.finance.branch_statement',
                            'icon' => 'heroicon-o-document-text',
                            'url' => fn() => \App\Filament\Pages\Finance\BranchStatementPage::getUrl(),
                            'permission' => 'finance_reports.view',
                        ],
                        [
                            'title' => 'sidebar.finance.import_excel',
                            'icon' => 'heroicon-o-arrow-up-tray',
                            'url' => fn() => \App\Filament\Pages\Finance\ImportBranchTransactionsPage::getUrl(),
                            'permission' => 'finance.view_any',
                        ],
                        [
                            'title' => 'sidebar.finance.income_statement_by_branch',
                            'icon' => 'heroicon-o-chart-pie',
                            'url' => fn() => \App\Filament\Pages\Finance\IncomeStatementByBranchPage::getUrl(),
                            'permission' => 'finance_reports.view',
                        ],
                        [
                            'title' => 'sidebar.finance.income_report',
                            'icon' => 'heroicon-o-arrow-trending-up',
                            'url' => fn() => \App\Filament\Pages\Finance\IncomeReportPage::getUrl(),
                            'permission' => 'finance_reports.view',
                        ],
                        [
                            'title' => 'sidebar.finance.expense_report',
                            'icon' => 'heroicon-o-arrow-trending-down',
                            'url' => fn() => \App\Filament\Pages\Finance\ExpenseReportPage::getUrl(),
                            'permission' => 'finance_reports.view',
                        ],
            ],
        ],
        [
            'title' => 'sidebar.employment',
            'icon' => 'heroicon-o-briefcase',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                        [
                            'title' => 'sidebar.employment.agents',
                            'icon' => 'heroicon-o-user-circle',
                            'url' => fn() => AgentResource::getUrl(),
                            'permission' => 'agents.view_any',
                        ],
                        [
                            'title' => 'sidebar.employment.agent_labor_prices',
                            'icon' => 'heroicon-o-currency-dollar',
                            'url' => fn() => AgentLaborPriceResource::getUrl(),
                            'permission' => 'agent_labor_prices.view_any',
                        ],
                        [
                            'title' => 'sidebar.employment.nationalities',
                            'icon' => 'heroicon-o-flag',
                            'url' => fn() => NationalityResource::getUrl(),
                            'permission' => 'nationalities.view_any',
                        ],
                        [
                            'title' => 'sidebar.employment.professions',
                            'icon' => 'heroicon-o-briefcase',
                            'url' => fn() => ProfessionResource::getUrl(),
                            'permission' => 'professions.view_any',
                        ],
                        [
                            'title' => 'sidebar.employment.laborers',
                            'icon' => 'heroicon-o-user-group',
                            'url' => fn() => LaborerResource::getUrl(),
                            'permission' => 'laborers.view_any',
                        ],
                        [
                            'title' => 'sidebar.employment.packages',
                            'icon' => 'heroicon-o-gift',
                            'url' => fn() => PackageResource::getUrl(),
                            'permission' => 'packages.view_any',
                        ],
                        [
                            'title' => 'sidebar.recruitment_contracts',
                            'icon' => 'heroicon-o-document-text',
                            'url' => fn() => RecruitmentContractResource::getUrl(),
                            'permission' => 'recruitment_contracts.view_any',
                        ],
                        [
                            'title' => 'sidebar.recruitment_contracts.add_contract',
                            'icon' => 'heroicon-o-plus-circle',
                            'url' => fn() => RecruitmentContractResource::getUrl('create'),
                            'permission' => 'recruitment_contracts.create',
                        ],
            ],
        ],
        [
            'title' => 'sidebar.rent_section',
            'icon' => 'heroicon-o-home-modern',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                        [
                            'title' => 'sidebar.rental.contracts',
                            'icon' => 'heroicon-o-document-text',
                            'url' => fn() => RentalContractResource::getUrl(),
                            'permission' => 'rental_contracts.view_any',
                        ],
                        [
                            'title' => 'sidebar.rental.requests',
                            'icon' => 'heroicon-o-clipboard-document-list',
                            'url' => fn() => RentalRequestsResource::getUrl(),
                            'permission' => 'rental_requests.view_any',
                            'badge' => 'housing_requests_count',
                        ],
                        [
                            'title' => 'sidebar.rental.cancel_refund_requests',
                            'icon' => 'heroicon-o-x-circle',
                            'url' => fn() => CancelRefundRequestsResource::getUrl(),
                            'permission' => 'cancel_refund_requests.view_any',
                        ],
                        [
                            'title' => 'sidebar.rental.returned_contracts',
                            'icon' => 'heroicon-o-arrow-path',
                            'url' => fn() => ReturnedContractsResource::getUrl(),
                            'permission' => 'returned_contracts.view_any',
                        ],
                        [
                            'title' => 'sidebar.rental.archived_contracts',
                            'icon' => 'heroicon-o-archive-box',
                            'url' => fn() => ArchivedContractsResource::getUrl(),
                            'permission' => 'archived_contracts.view_any',
                        ],
                        [
                            'title' => 'sidebar.rental.reports',
                            'icon' => 'heroicon-o-chart-bar',
                            'url' => fn() => \App\Filament\Pages\Rental\RentalReportsPage::getUrl(),
                            'permission' => 'rental_reports.view',
                        ],
                        [
                            'title' => 'sidebar.available_workers',
                            'icon' => 'heroicon-o-user-group',
                            'url' => fn() => LaborerResource::getUrl(),
                            'permission' => 'laborers.view_any',
                            'badge' => 'available_workers_count',
                        ],
                        [
                            'title' => 'sidebar.rental_housing.accommodation_entries',
                            'icon' => 'heroicon-o-building-office',
                            'url' => fn() => \App\Filament\Pages\Housing\Rental\RentalAccommodationEntryPage::getUrl(),
                            'permission' => 'housing.accommodation_entries.create',
                        ],
            ],
        ],
        [
            'title' => 'sidebar.housing_management',
            'icon' => 'heroicon-o-cog-6-tooth',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                [
                    'title' => 'sidebar.housing_management.status_management',
                    'icon' => 'heroicon-o-tag',
                    'url' => fn() => \App\Filament\Resources\Housing\HousingStatusResource::getUrl(),
                    'permission' => 'housing.statuses.view_any',
                ],
                [
                    'title' => 'sidebar.housing_management.buildings_management',
                    'icon' => 'heroicon-o-building-office-2',
                    'url' => fn() => \App\Filament\Resources\Housing\BuildingResource::getUrl(),
                    'permission' => 'housing.buildings.view_any',
                ],
                [
                    'title' => 'sidebar.housing.drivers',
                    'icon' => 'heroicon-o-truck',
                    'url' => fn() => \App\Filament\Resources\Housing\HousingDriverResource::getUrl(),
                    'permission' => 'housing.drivers.view_any',
                ],
            ],
        ],
        [
            'title' => 'sidebar.system',
            'icon' => 'heroicon-o-cog-6-tooth',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                        [
                            'title' => 'sidebar.system.users',
                            'icon' => 'heroicon-o-users',
                            'url' => fn() => \App\Filament\Resources\UserResource::getUrl(),
                            'permission' => 'users.view_any',
                        ],
                        [
                            'title' => 'sidebar.system.roles',
                            'icon' => 'heroicon-o-shield-check',
                            'url' => fn() => \App\Filament\Resources\RoleResource::getUrl(),
                            'permission' => 'roles.view_any',
                        ],
                        [
                            'title' => 'sidebar.system.permissions',
                            'icon' => 'heroicon-o-key',
                            'url' => fn() => \App\Filament\Resources\PermissionResource::getUrl(),
                            'permission' => 'permissions.view_any',
                        ],
            ],
        ],
        [
            'title' => 'sidebar.hr',
            'icon' => 'heroicon-o-users',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                [
                    'title' => 'sidebar.hr.basic_settings',
                    'icon' => 'heroicon-o-cog-6-tooth',
                    'url' => null,
                    'permission' => null,
                    'children' => [
                        [
                            'title' => 'sidebar.hr.departments',
                            'icon' => 'heroicon-o-building-office',
                            'url' => fn() => \App\Filament\Resources\HR\DepartmentResource::getUrl(),
                            'permission' => 'hr_departments.view_any',
                        ],
                        [
                            'title' => 'sidebar.hr.positions',
                            'icon' => 'heroicon-o-briefcase',
                            'url' => fn() => \App\Filament\Resources\HR\PositionResource::getUrl(),
                            'permission' => 'hr_positions.view_any',
                        ],
                        [
                            'title' => 'sidebar.hr.identity_types',
                            'icon' => 'heroicon-o-identification',
                            'url' => fn() => \App\Filament\Resources\HR\IdentityTypeResource::getUrl(),
                            'permission' => 'hr_identity_types.view_any',
                        ],
                        [
                            'title' => 'sidebar.hr.blood_types',
                            'icon' => 'heroicon-o-heart',
                            'url' => fn() => \App\Filament\Resources\HR\BloodTypeResource::getUrl(),
                            'permission' => 'hr_blood_types.view_any',
                        ],
                        [
                            'title' => 'sidebar.hr.banks',
                            'icon' => 'heroicon-o-building-library',
                            'url' => fn() => \App\Filament\Resources\HR\BankResource::getUrl(),
                            'permission' => 'hr_banks.view_any',
                        ],
                    ],
                ],
                [
                    'title' => 'sidebar.hr.work_places',
                    'icon' => 'heroicon-o-map-pin',
                    'url' => fn() => \App\Filament\Resources\HR\WorkPlaceResource::getUrl(),
                    'permission' => 'hr_work_places.view_any',
                ],
                [
                    'title' => 'sidebar.hr.assign_work_places',
                    'icon' => 'heroicon-o-user-plus',
                    'url' => fn() => \App\Filament\Pages\HR\AssignWorkPlacesPage::getUrl(),
                    'permission' => 'hr_assign_work_places.view',
                ],
                [
                    'title' => 'sidebar.hr.devices',
                    'icon' => 'heroicon-o-finger-print',
                    'url' => fn() => \App\Filament\Resources\HR\DeviceResource::getUrl(),
                    'permission' => 'hr_devices.view_any',
                ],
                [
                    'title' => 'sidebar.hr.biometric_attendance',
                    'icon' => 'heroicon-o-clock',
                    'url' => fn() => \App\Filament\Resources\Biometric\BiometricAttendanceResource::getUrl(),
                    'permission' => 'biometric_attendance.view_any',
                ],
                [
                    'title' => 'sidebar.hr.employees',
                    'icon' => 'heroicon-o-user-group',
                    'url' => fn() => \App\Filament\Resources\HR\EmployeeResource::getUrl(),
                    'permission' => 'hr_employees.view_any',
                ],
                [
                    'title' => 'sidebar.hr.employee_groups',
                    'icon' => 'heroicon-o-user-group',
                    'url' => fn() => \App\Filament\Resources\HR\EmployeeGroupResource::getUrl(),
                    'permission' => 'hr_employee_groups.view_any',
                ],
                [
                    'title' => 'sidebar.hr.employee_financial',
                    'icon' => 'heroicon-o-currency-dollar',
                    'url' => fn() => \App\Filament\Resources\HR\EmployeeFinancialProfileResource::getUrl(),
                    'permission' => 'hr_employee_financial.view_any',
                ],
                [
                    'title' => 'sidebar.hr.work_schedules',
                    'icon' => 'heroicon-o-calendar',
                    'url' => fn() => \App\Filament\Resources\HR\WorkScheduleResource::getUrl(),
                    'permission' => 'hr_work_schedules.view_any',
                ],
                [
                    'title' => 'sidebar.hr.copy_schedules',
                    'icon' => 'heroicon-o-document-duplicate',
                    'url' => fn() => \App\Filament\Pages\HR\CopySchedulesPage::getUrl(),
                    'permission' => 'hr_schedule_copy.view',
                ],
                [
                    'title' => 'sidebar.hr.daily_attendance',
                    'icon' => 'heroicon-o-clock',
                    'url' => fn() => \App\Filament\Pages\HR\DailyAttendancePage::getUrl(),
                    'permission' => 'hr_attendance_daily.view',
                ],
                [
                    'title' => 'sidebar.hr.monthly_attendance_report',
                    'icon' => 'heroicon-o-chart-bar',
                    'url' => fn() => \App\Filament\Pages\HR\MonthlyAttendanceReportPage::getUrl(),
                    'permission' => 'hr_attendance_monthly.view',
                ],
                [
                    'title' => 'sidebar.hr.leave_types',
                    'icon' => 'heroicon-o-calendar-days',
                    'url' => fn() => \App\Filament\Resources\HR\LeaveTypeResource::getUrl(),
                    'permission' => 'hr_leave_types.view_any',
                ],
                [
                    'title' => 'sidebar.hr.leave_balance',
                    'icon' => 'heroicon-o-scale',
                    'url' => fn() => \App\Filament\Pages\HR\LeaveBalancePage::getUrl(),
                    'permission' => 'hr_leave_balance.view',
                ],
                [
                    'title' => 'sidebar.hr.leave_requests',
                    'icon' => 'heroicon-o-clipboard-document-check',
                    'url' => fn() => \App\Filament\Resources\HR\LeaveRequestResource::getUrl(),
                    'permission' => 'hr_leave_requests.view_any',
                ],
                [
                    'title' => 'sidebar.hr.holidays',
                    'icon' => 'heroicon-o-calendar',
                    'url' => fn() => \App\Filament\Resources\HR\HolidayResource::getUrl(),
                    'permission' => 'hr_holidays.view_any',
                ],
                [
                    'title' => 'sidebar.hr.holidays_calendar',
                    'icon' => 'heroicon-o-calendar-days',
                    'url' => fn() => \App\Filament\Pages\HR\HolidaysCalendarPage::getUrl(),
                    'permission' => 'hr_holidays.view',
                ],
                [
                    'title' => 'sidebar.hr.leave_report',
                    'icon' => 'heroicon-o-chart-bar',
                    'url' => fn() => \App\Filament\Pages\HR\LeaveReportPage::getUrl(),
                    'permission' => 'hr_leave_reports.view',
                ],
                [
                    'title' => 'sidebar.hr.loan_types',
                    'icon' => 'heroicon-o-banknotes',
                    'url' => fn() => \App\Filament\Resources\HR\LoanTypeResource::getUrl(),
                    'permission' => 'hr_loan_types.view_any',
                ],
                [
                    'title' => 'sidebar.hr.loans',
                    'icon' => 'heroicon-o-currency-dollar',
                    'url' => fn() => \App\Filament\Resources\HR\LoanResource::getUrl(),
                    'permission' => 'hr_loans.view_any',
                ],
                [
                    'title' => 'sidebar.hr.salary_components',
                    'icon' => 'heroicon-o-banknotes',
                    'url' => fn() => \App\Filament\Resources\HR\SalaryComponentResource::getUrl(),
                    'permission' => 'hr_components.view_any',
                ],
                [
                    'title' => 'sidebar.hr.payroll',
                    'icon' => 'heroicon-o-currency-dollar',
                    'url' => fn() => \App\Filament\Resources\HR\PayrollRunResource::getUrl(),
                    'permission' => 'hr_payroll.view_any',
                ],
                [
                    'title' => 'sidebar.hr.excuse_requests',
                    'icon' => 'heroicon-o-clock',
                    'url' => fn() => \App\Filament\Resources\HR\ExcuseRequestResource::getUrl(),
                    'permission' => 'hr_excuse_requests.view_any',
                ],
            ],
        ],
    ],
];
