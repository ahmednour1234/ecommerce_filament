<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\Reports\Concerns\HasFinanceReportFilters;
use App\Filament\Resources\Finance\Reports\Widgets\IncomeExpenseBreakdownDonut;
use App\Filament\Resources\Finance\Reports\Widgets\IncomeExpenseTrendChart;
use App\Models\Finance\BranchTransaction;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FinanceIncomeExpenseReport extends Page implements HasForms
{
    use TranslatableNavigation;
    use InteractsWithForms;
    use HasFinanceReportFilters;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'finance';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Finance Reports - Income & Expense';
    protected static ?string $navigationTranslationKey = 'sidebar.finance.income_expenses';

    protected static string $view = 'filament.finance.reports.income-expense-report';

    public function mount(): void
    {
        $this->initDefaultDates();
        $this->form->fill([
            'from' => $this->from,
            'to'   => $this->to,
            'group_by' => $this->group_by,
        ]);
    }

    // ✅ ده بيخلي الفورم يشتغل
    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $this->filtersForm($form);
    }

    public function baseQuery()
    {
        [$from, $to] = $this->dateRange();

        $q = BranchTransaction::query()
            ->with(['branch', 'country', 'currency', 'financeType'])
            ->whereBetween('trx_date', [$from, $to]);

        /** @var User|null $user */
        $user = Auth::user();
        if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($branchIds)) {
                $q->whereIn('branch_id', $branchIds);
            } else {
                $q->whereRaw('1 = 0');
            }
        }

        $q->when($this->branch_id, fn($qq) => $qq->where('branch_id', $this->branch_id));
        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));

        return $q;
    }

    public function kpis(): array
    {
        $q = $this->baseQuery();

        $income  = (clone $q)->whereHas('financeType', fn($qq) => $qq->where('kind', 'income'))->sum('amount');
        $expense = (clone $q)->whereHas('financeType', fn($qq) => $qq->where('kind', 'expense'))->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'net' => (float) ($income - $expense),
            'count' => (int) (clone $q)->count(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeExpenseTrendChart::class,
            IncomeExpenseBreakdownDonut::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'sm' => 1,
            'lg' => 2,
        ];
    }

    // ✅ نبعث الفلاتر للـ widgets
    public function getWidgetData(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'branch_id' => $this->branch_id,
            'country_id' => $this->country_id,
            'currency_id' => $this->currency_id,
            'group_by' => $this->group_by,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->can('finance_reports.view') ?? false;
    }
}
