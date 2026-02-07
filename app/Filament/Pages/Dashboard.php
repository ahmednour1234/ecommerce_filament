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
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
class Dashboard extends BaseDashboard implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    use InteractsWithForms;

    /**
     * Used by the modal form (Filament Action form state)
     */
    public array $formState = [];

    /**
     * Parsed + validated filters used by widgets
     */
    public array $filters = [];

    /**
     * Livewire event listeners for auto-opening filters modal
     */
    protected $listeners = ['open-dashboard-filters' => 'openFiltersModal'];

    public function mount(): void
    {
        parent::mount();

        // Parse from query string (request)
        $this->filters = DashboardFilterHelper::parseFiltersFromRequest();
        $this->filters = DashboardFilterHelper::validateDateRange($this->filters);

        // Prepare modal form state
        $this->formState = [
            'date_from' => $this->filters['date_from']?->format('Y-m-d'),
            'date_to' => $this->filters['date_to']?->format('Y-m-d'),
            'branch_id' => $this->filters['branch_id'],
            'transaction_type' => $this->filters['transaction_type'] ?? 'all',
            'finance_type_id' => $this->filters['finance_type_id'],
            'revenue_type_id' => $this->filters['revenue_type_id'],
            'expense_type_id' => $this->filters['expense_type_id'],
            'currency_id' => $this->filters['currency_id'],
            'order_status' => $this->filters['order_status'] ?? 'all',
        ];
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Check if filters are currently applied (date_from and date_to in query string OR ?filters=1 flag)
     */
    public function hasFiltersApplied(): bool
    {
        $request = request();
        return ($request->has('date_from') && $request->has('date_to')) || $request->boolean('filters');
    }

    /**
     * Open the filters modal programmatically (called by Livewire event from Alpine.js)
     */
    public function openFiltersModal(): void
    {
        $this->mountAction('filters');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filters')
                ->label('الفلاتر')
                ->icon('heroicon-o-funnel')
                ->color('primary')
                ->button()
                ->modalHeading('فلاتر لوحة التحكم')
                ->modalDescription('اختر الفترة والفروع/الأنواع لتصفية كل الإحصائيات والجداول والشارتات.')
                ->modalWidth('4xl')
                ->modalSubmitActionLabel('تطبيق')
                ->modalCancelActionLabel('إغلاق')
                ->fillForm(fn () => $this->formState)
                ->form($this->filtersSchema())
                ->action(function (array $data) {
                    $this->applyFilters($data);
                })
                ->extraModalFooterActions([
                    Action::make('resetFilters')
                        ->label('Reset')
                        ->color('gray')
                        ->action(function () {
                            $this->resetToDefaults();
                        }),
                ]),
        ];
    }

    private function filtersSchema(): array
    {
        return [
            DatePicker::make('date_from')
                ->label('من تاريخ')
                ->required()
                ->default(fn () => $this->formState['date_from'] ?? now()->startOfMonth()->format('Y-m-d'))
                ->live(),

            DatePicker::make('date_to')
                ->label('إلى تاريخ')
                ->required()
                ->default(fn () => $this->formState['date_to'] ?? now()->endOfMonth()->format('Y-m-d'))
                ->live(),

            Select::make('branch_id')
                ->label('الفرع')
                ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('جميع الفروع')
                ->nullable()
                ->default(fn () => $this->formState['branch_id'] ?? null)
                ->live(),

            Select::make('transaction_type')
                ->label('نوع المعاملة')
                ->options([
                    'all' => 'الكل',
                    'revenue' => 'إيرادات',
                    'expense' => 'مصروفات',
                ])
                ->default(fn () => $this->formState['transaction_type'] ?? 'all')
                ->live(),

            Select::make('finance_type_id')
                ->label('نوع المالية')
                ->options(fn () => FinanceType::where('is_active', true)->pluck('name_text', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('جميع الأنواع')
                ->nullable()
                ->default(fn () => $this->formState['finance_type_id'] ?? null)
                ->live(),

            Select::make('revenue_type_id')
                ->label('نوع الإيراد')
                ->options(fn () => FinanceType::where('is_active', true)->where('kind', 'income')->pluck('name_text', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('جميع أنواع الإيرادات')
                ->nullable()
                ->visible(fn ($get) => in_array($get('transaction_type'), ['revenue', 'all']))
                ->default(fn () => $this->formState['revenue_type_id'] ?? null)
                ->live(),

            Select::make('expense_type_id')
                ->label('نوع المصروف')
                ->options(fn () => FinanceType::where('is_active', true)->where('kind', 'expense')->pluck('name_text', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('جميع أنواع المصروفات')
                ->nullable()
                ->visible(fn ($get) => in_array($get('transaction_type'), ['expense', 'all']))
                ->default(fn () => $this->formState['expense_type_id'] ?? null)
                ->live(),

            Select::make('currency_id')
                ->label('العملة')
                ->options(fn () => Currency::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('جميع العملات')
                ->nullable()
                ->default(fn () => $this->formState['currency_id'] ?? null)
                ->live(),

            Select::make('order_status')
                ->label('حالة الطلب')
                ->options([
                    'all' => 'الكل',
                    'pending' => 'قيد الانتظار',
                    'processing' => 'قيد المعالجة',
                    'completed' => 'مكتمل',
                    'cancelled' => 'ملغي',
                    'refunded' => 'مسترد',
                ])
                ->default(fn () => $this->formState['order_status'] ?? 'all')
                ->live(),
        ];
    }

    private function applyFilters(array $data): void
    {
        // Save form state
        $this->formState = $data;

        // Build internal filters
        $this->filters = [
            'date_from' => !empty($data['date_from']) ? Carbon::parse($data['date_from'])->startOfDay() : null,
            'date_to' => !empty($data['date_to']) ? Carbon::parse($data['date_to'])->endOfDay() : null,
            'branch_id' => !empty($data['branch_id']) ? (int) $data['branch_id'] : null,
            'transaction_type' => $data['transaction_type'] ?? 'all',
            'finance_type_id' => !empty($data['finance_type_id']) ? (int) $data['finance_type_id'] : null,
            'revenue_type_id' => !empty($data['revenue_type_id']) ? (int) $data['revenue_type_id'] : null,
            'expense_type_id' => !empty($data['expense_type_id']) ? (int) $data['expense_type_id'] : null,
            'currency_id' => !empty($data['currency_id']) ? (int) $data['currency_id'] : null,
            'order_status' => (!empty($data['order_status']) && $data['order_status'] !== 'all') ? $data['order_status'] : null,
        ];

        $this->filters = DashboardFilterHelper::validateDateRange($this->filters);

        $queryString = DashboardFilterHelper::buildFilterQueryString($this->filters);

        $this->redirect(
            route('filament.admin.pages.dashboard') . ($queryString ? '?' . $queryString : ''),
            navigate: true
        );
    }

    private function resetToDefaults(): void
    {
        $defaults = DashboardFilterHelper::getDefaultFilters();

        $data = [
            'date_from' => $defaults['date_from']->format('Y-m-d'),
            'date_to' => $defaults['date_to']->format('Y-m-d'),
            'branch_id' => null,
            'transaction_type' => 'all',
            'finance_type_id' => null,
            'revenue_type_id' => null,
            'expense_type_id' => null,
            'currency_id' => null,
            'order_status' => 'all',
        ];

        $this->applyFilters($data);
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
