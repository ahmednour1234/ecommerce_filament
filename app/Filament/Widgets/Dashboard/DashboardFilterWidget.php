<?php

namespace App\Filament\Widgets\Dashboard;

use App\Helpers\DashboardFilterHelper;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class DashboardFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.dashboard-filter-widget';
    protected int|string|array $columnSpan = 'full';

    public array $formState = [];

    public function mount(): void
    {
        $filters = DashboardFilterHelper::parseFiltersFromRequest();
        $filters = DashboardFilterHelper::validateDateRange($filters);

        $this->formState = [
            'date_from' => $filters['date_from']?->format('Y-m-d'),
            'date_to' => $filters['date_to']?->format('Y-m-d'),
            'branch_id' => $filters['branch_id'],
            'transaction_type' => $filters['transaction_type'] ?? 'all',
            'finance_type_id' => $filters['finance_type_id'],
            'revenue_type_id' => $filters['revenue_type_id'],
            'expense_type_id' => $filters['expense_type_id'],
            'currency_id' => $filters['currency_id'],
            'order_status' => $filters['order_status'] ?? 'all',
        ];

        $this->filterForm->fill($this->formState);
    }

    protected function getForms(): array
    {
        return [
            'filterForm',
        ];
    }

    protected function getFilterFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    DatePicker::make('date_from')
                        ->label('Start date')
                        ->required()
                        ->default(fn () => $this->formState['date_from'] ?? now()->startOfMonth()->format('Y-m-d'))
                        ->displayFormat('d/m/Y')
                        ->native(false),

                    DatePicker::make('date_to')
                        ->label('End date')
                        ->required()
                        ->default(fn () => $this->formState['date_to'] ?? now()->endOfMonth()->format('Y-m-d'))
                        ->displayFormat('d/m/Y')
                        ->native(false),
                ]),
        ];
    }

    public function applyFilters(): void
    {
        $data = $this->filterForm->getState();

        $this->formState = $data;

        $filters = [
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

        $filters = DashboardFilterHelper::validateDateRange($filters);
        $queryString = DashboardFilterHelper::buildFilterQueryString($filters);

        $this->redirect(
            request()->url() . ($queryString ? '?' . $queryString : ''),
            navigate: true
        );
    }
}
