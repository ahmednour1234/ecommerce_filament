<?php

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\Rental\RentalRequestsResource;
use App\Filament\Resources\Rental\RentalContractResource;
use App\Filament\Resources\Recruitment\LaborerResource;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;

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
            'title' => 'sidebar.main_settings',
            'icon' => 'heroicon-o-cog-6-tooth',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                [
                    'title' => 'sidebar.main_settings.branches',
                    'icon' => 'heroicon-o-building-office',
                    'url' => fn() => \App\Filament\Resources\MainCore\BranchResource::getUrl(),
                    'permission' => 'branches.view_any',
                ],
                [
                    'title' => 'sidebar.main_settings.currencies',
                    'icon' => 'heroicon-o-currency-dollar',
                    'url' => fn() => \App\Filament\Resources\MainCore\CurrencyResource::getUrl(),
                    'permission' => 'currencies.view_any',
                ],
                [
                    'title' => 'sidebar.main_settings.languages',
                    'icon' => 'heroicon-o-language',
                    'url' => fn() => \App\Filament\Resources\MainCore\LanguageResource::getUrl(),
                    'permission' => 'languages.view_any',
                ],
                [
                    'title' => 'sidebar.main_settings.translations',
                    'icon' => 'heroicon-o-document-text',
                    'url' => fn() => \App\Filament\Resources\MainCore\TranslationResource::getUrl(),
                    'permission' => 'translations.view_any',
                ],
            ],
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
                    'title' => 'sidebar.finance.accounts',
                    'icon' => 'heroicon-o-wallet',
                    'url' => fn() => \App\Filament\Resources\Accounting\AccountResource::getUrl(),
                    'permission' => 'accounts.view_any',
                ],
                [
                    'title' => 'sidebar.finance.journals',
                    'icon' => 'heroicon-o-book-open',
                    'url' => fn() => \App\Filament\Resources\Accounting\JournalResource::getUrl(),
                    'permission' => 'journals.view_any',
                ],
                [
                    'title' => 'sidebar.finance.vouchers',
                    'icon' => 'heroicon-o-document-duplicate',
                    'url' => fn() => \App\Filament\Resources\Accounting\VoucherResource::getUrl(),
                    'permission' => 'vouchers.view_any',
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
                    'title' => 'sidebar.employment.recruitment_contracts',
                    'icon' => 'heroicon-o-document-text',
                    'url' => fn() => RecruitmentContractResource::getUrl(),
                    'permission' => 'recruitment_contracts.view_any',
                ],
                [
                    'title' => 'sidebar.employment.agents',
                    'icon' => 'heroicon-o-user-circle',
                    'url' => fn() => \App\Filament\Resources\Recruitment\AgentResource::getUrl(),
                    'permission' => 'agents.view_any',
                ],
                [
                    'title' => 'sidebar.employment.professions',
                    'icon' => 'heroicon-o-briefcase',
                    'url' => fn() => \App\Filament\Resources\Recruitment\ProfessionResource::getUrl(),
                    'permission' => 'professions.view_any',
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
                    'title' => 'sidebar.recruitment_housing',
                    'icon' => 'heroicon-o-building-office-2',
                    'url' => null,
                    'permission' => null,
                    'badge' => null,
                    'children' => [
                        [
                            'title' => 'sidebar.recruitment_housing.contracts',
                            'icon' => 'heroicon-o-document-text',
                            'url' => fn() => RecruitmentContractResource::getUrl(),
                            'permission' => 'recruitment_contracts.view_any',
                        ],
                    ],
                ],
                [
                    'title' => 'sidebar.rent_housing',
                    'icon' => 'heroicon-o-home',
                    'url' => null,
                    'permission' => null,
                    'badge' => null,
                    'children' => [
                        [
                            'title' => 'sidebar.rent_housing.contracts',
                            'icon' => 'heroicon-o-document-text',
                            'url' => fn() => RentalContractResource::getUrl(),
                            'permission' => 'rental_contracts.view_any',
                        ],
                    ],
                ],
                [
                    'title' => 'sidebar.available_workers',
                    'icon' => 'heroicon-o-user-group',
                    'url' => fn() => LaborerResource::getUrl(),
                    'permission' => 'laborers.view_any',
                    'badge' => 'available_workers_count',
                ],
                [
                    'title' => 'sidebar.housing_requests',
                    'icon' => 'heroicon-o-clipboard-document-list',
                    'url' => fn() => RentalRequestsResource::getUrl(),
                    'permission' => 'rental_requests.view_any',
                    'badge' => 'housing_requests_count',
                ],
                [
                    'title' => 'sidebar.workers_salaries',
                    'icon' => 'heroicon-o-banknotes',
                    'url' => null,
                    'permission' => null,
                    'badge' => null,
                ],
                [
                    'title' => 'sidebar.workers_vacations',
                    'icon' => 'heroicon-o-calendar',
                    'url' => null,
                    'permission' => null,
                    'badge' => null,
                ],
            ],
        ],
        [
            'title' => 'sidebar.reports',
            'icon' => 'heroicon-o-chart-bar',
            'url' => null,
            'permission' => null,
            'badge' => null,
            'children' => [
                [
                    'title' => 'sidebar.reports.finance',
                    'icon' => 'heroicon-o-currency-dollar',
                    'url' => fn() => \App\Filament\Resources\Finance\Reports\Pages\FinanceIncomeExpenseReport::getUrl(),
                    'permission' => 'finance_reports.view',
                ],
                [
                    'title' => 'sidebar.reports.hr',
                    'icon' => 'heroicon-o-users',
                    'url' => fn() => \App\Filament\Pages\HR\LeaveReportPage::getUrl(),
                    'permission' => 'hr_leave_reports.view',
                ],
            ],
        ],
    ],
];
