<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesTableWidget;
use App\Filament\Widgets\Dashboard\FinanceStatsWidget;
use App\Filament\Widgets\Dashboard\FinanceTopTypesWidget;
use App\Filament\Widgets\Dashboard\HRStatsWidget;
use App\Filament\Widgets\Dashboard\OrderStatsWidget;
use App\Helpers\DashboardFilterHelper;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Dashboard extends BaseDashboard implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public ?array $data = [];
    public array $filters = [];

    public function mount(): void
    {
        parent::mount();

        $this->filters = DashboardFilterHelper::parseFiltersFromRequest();

        $this->data = [
            'date_from' => $this->filters['date_from']?->format('Y-m-d'),
            'date_to' => $this->filters['date_to']?->format('Y-m-d'),
            'branch_id' => $this->filters['branch_id'],
            'transaction_type' => $this->filters['transaction_type'],
            'finance_type_id' => $this->filters['finance_type_id'],
            'revenue_type_id' => $this->filters['revenue_type_id'],
            'expense_type_id' => $this->filters['expense_type_id'],
            'currency_id' => $this->filters['currency_id'],
            'order_status' => $this->filters['order_status'],
        ];

        $this->form->fill($this->data);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('الفلاتر')
                    ->description('استخدم الفلاتر أدناه لتصفية البيانات')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('date_from')
                            ->label('من تاريخ')
                            ->default(fn () => now()->startOfMonth()->format('Y-m-d'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\DatePicker::make('date_to')
                            ->label('إلى تاريخ')
                            ->default(fn () => now()->endOfMonth()->format('Y-m-d'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('branch_id')
                            ->label('الفرع')
                            ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع الفروع')
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('transaction_type')
                            ->label('نوع المعاملة')
                            ->options([
                                'all' => 'الكل',
                                'revenue' => 'إيرادات',
                                'expense' => 'مصروفات',
                            ])
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('finance_type_id')
                            ->label('نوع المالية')
                            ->options(fn () => FinanceType::where('is_active', true)->get()->pluck('name_text', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع الأنواع')
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('revenue_type_id')
                            ->label('نوع الإيراد')
                            ->options(fn () => FinanceType::where('is_active', true)->where('kind', 'income')->get()->pluck('name_text', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع أنواع الإيرادات')
                            ->nullable()
                            ->visible(fn ($get) => $get('transaction_type') === 'revenue' || $get('transaction_type') === 'all')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('expense_type_id')
                            ->label('نوع المصروف')
                            ->options(fn () => FinanceType::where('is_active', true)->where('kind', 'expense')->get()->pluck('name_text', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع أنواع المصروفات')
                            ->nullable()
                            ->visible(fn ($get) => $get('transaction_type') === 'expense' || $get('transaction_type') === 'all')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('currency_id')
                            ->label('العملة')
                            ->options(fn () => Currency::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع العملات')
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),

                        \Filament\Forms\Components\Select::make('order_status')
                            ->label('حالة الطلب')
                            ->options([
                                'all' => 'الكل',
                                'pending' => 'قيد الانتظار',
                                'processing' => 'قيد المعالجة',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                                'refunded' => 'مسترد',
                            ])
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateFilters();
                            }),
                    ])
                    ->columns(5)
                    ->collapsible()
                    ->collapsed(false)
                    ->persistentCollapsed(false)
                    ->footerActions([
                        Action::make('reset')
                            ->label('إعادة تعيين')
                            ->color('gray')
                            ->action(function () {
                                $defaults = DashboardFilterHelper::getDefaultFilters();
                                $this->data = [
                                    'date_from' => $defaults['date_from']->format('Y-m-d'),
                                    'date_to' => $defaults['date_to']->format('Y-m-d'),
                                    'branch_id' => null,
                                    'transaction_type' => 'all',
                                    'finance_type_id' => null,
                                    'revenue_type_id' => null,
                                    'expense_type_id' => null,
                                    'currency_id' => null,
                                    'order_status' => null,
                                ];
                                $this->form->fill($this->data);
                                $this->updateFilters();
                            }),
                    ]),
            ])
            ->statePath('data')
            ->live();
    }

    public function updateFilters(): void
    {
        $this->filters = [
            'date_from' => $this->data['date_from'] ? Carbon::parse($this->data['date_from'])->startOfDay() : null,
            'date_to' => $this->data['date_to'] ? Carbon::parse($this->data['date_to'])->endOfDay() : null,
            'branch_id' => $this->data['branch_id'] ? (int) $this->data['branch_id'] : null,
            'transaction_type' => $this->data['transaction_type'] ?? 'all',
            'finance_type_id' => $this->data['finance_type_id'] ? (int) $this->data['finance_type_id'] : null,
            'revenue_type_id' => $this->data['revenue_type_id'] ? (int) $this->data['revenue_type_id'] : null,
            'expense_type_id' => $this->data['expense_type_id'] ? (int) $this->data['expense_type_id'] : null,
            'currency_id' => $this->data['currency_id'] ? (int) $this->data['currency_id'] : null,
            'order_status' => $this->data['order_status'] && $this->data['order_status'] !== 'all' ? $this->data['order_status'] : null,
        ];

        $this->filters = DashboardFilterHelper::validateDateRange($this->filters);

        $queryString = DashboardFilterHelper::buildFilterQueryString($this->filters);
        
        $this->redirect(route('filament.admin.pages.dashboard') . ($queryString ? '?' . $queryString : ''), navigate: true);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatsWidget::class,
            FinanceStatsWidget::class,
            HRStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            FinanceTopTypesWidget::class,
            FinanceBranchesComparisonChartWidget::class,
            FinanceBranchesTableWidget::class,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}

