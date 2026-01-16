<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\Reports\Widgets\IncomeExpenseDonutChart;
use App\Filament\Resources\Finance\Reports\Widgets\IncomeExpenseTrendChart;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Filament\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class IncomeExpenseReport extends Page implements HasForms, HasTable
{
    use TranslatableNavigation;
    use InteractsWithForms;
    use InteractsWithTable;
    use ExportsResourceTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 60;

    protected static ?string $navigationTranslationKey = 'sidebar.finance.reports.income_expense';
    protected static ?string $title = 'Income & Expense Report';

    protected static string $view = 'filament.finance.reports.income-expense-report';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('finance_reports.view'), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard'))
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => auth()->user()?->can('finance_reports.export'))
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard'))
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => auth()->user()?->can('finance_reports.export'))
                ->action(fn () => $this->exportToPdf()),

            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard'))
                ->icon('heroicon-o-printer')
                ->visible(fn () => auth()->user()?->can('finance_reports.print'))
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    // عنوان التصدير للـ trait
    protected function getExportTitle(): ?string
    {
        return tr('reports.income_expense.title', [], null, 'dashboard');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->reportQuery())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('period')
                    ->label(tr('reports.columns.period', [], null, 'dashboard'))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('income')
                    ->label(tr('reports.columns.income', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 2))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('expense')
                    ->label(tr('reports.columns.expense', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 2))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('net')
                    ->label(tr('reports.columns.net', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 2))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('transaction_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label(tr('reports.filters.from', [], null, 'dashboard'))
                            ->default(now()->startOfMonth()),
                        \Filament\Forms\Components\DatePicker::make('to')
                            ->label(tr('reports.filters.to', [], null, 'dashboard'))
                            ->default(now()),
                    ]),
                SelectFilter::make('branch_id')
                    ->label(tr('reports.filters.branch', [], null, 'dashboard'))
                    ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('country_id')
                    ->label(tr('reports.filters.country', [], null, 'dashboard'))
                    ->options(fn () => Country::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('currency_id')
                    ->label(tr('reports.filters.currency', [], null, 'dashboard'))
                    ->options(fn () => Currency::where('is_active', true)->pluck('code', 'id'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label(tr('reports.filters.status', [], null, 'dashboard'))
                    ->options([
                        'pending'  => tr('tables.branch_tx.status_pending', [], null, 'dashboard'),
                        'approved' => tr('tables.branch_tx.status_approved', [], null, 'dashboard'),
                        'rejected' => tr('tables.branch_tx.status_rejected', [], null, 'dashboard'),
                    ]),
                SelectFilter::make('group_by')
                    ->label(tr('reports.filters.group_by', [], null, 'dashboard'))
                    ->options([
                        'day'   => tr('reports.filters.group_by_day', [], null, 'dashboard'),
                        'month' => tr('reports.filters.group_by_month', [], null, 'dashboard'),
                    ])
                    ->default('day'),
            ])
            ->paginated(false);
    }

    protected function reportQuery(): Builder
    {
        $filters = $this->tableFilters ?? [];
        $dateFilter = $filters['transaction_date'] ?? [];
        $from = isset($dateFilter['from']) && $dateFilter['from'] ? Carbon::parse($dateFilter['from'])->startOfDay() : now()->startOfMonth()->startOfDay();
        $to = isset($dateFilter['to']) && $dateFilter['to'] ? Carbon::parse($dateFilter['to'])->endOfDay() : now()->endOfDay();
        $groupBy = $filters['group_by'] ?? 'day';

        if ($groupBy === 'month') {
            $periodExpr = "DATE_FORMAT(branch_transactions.transaction_date, '%Y-%m')";
        } else {
            $periodExpr = "DATE(branch_transactions.transaction_date)";
        }

        $q = BranchTransaction::query()
            ->whereBetween('branch_transactions.transaction_date', [$from, $to])
            ->whereNull('branch_transactions.deleted_at');

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_transactions.branch_id', auth()->user()?->branch_id);
        }

        if (isset($filters['branch_id'])) {
            $q->where('branch_transactions.branch_id', $filters['branch_id']);
        }
        if (isset($filters['country_id'])) {
            $q->where('branch_transactions.country_id', $filters['country_id']);
        }
        if (isset($filters['currency_id'])) {
            $q->where('branch_transactions.currency_id', $filters['currency_id']);
        }
        if (isset($filters['status'])) {
            $q->where('branch_transactions.status', $filters['status']);
        }

        return $q->selectRaw("
                {$periodExpr} as id,
                {$periodExpr} as period,
                SUM(CASE WHEN branch_transactions.type='income' THEN branch_transactions.amount ELSE 0 END) as income,
                SUM(CASE WHEN branch_transactions.type='expense' THEN branch_transactions.amount ELSE 0 END) as expense,
                (SUM(CASE WHEN branch_transactions.type='income' THEN branch_transactions.amount ELSE 0 END) - SUM(CASE WHEN branch_transactions.type='expense' THEN branch_transactions.amount ELSE 0 END)) as net
            ")
            ->groupByRaw($periodExpr)
            ->orderByRaw($periodExpr . ' ASC');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeExpenseTrendChart::class,
            IncomeExpenseDonutChart::class,
        ];
    }

    public function getWidgetData(): array
    {
        $tableFilters = $this->tableFilters ?? [];
        $dateFilter = $tableFilters['transaction_date'] ?? [];

        $branchId = $tableFilters['branch_id'] ?? null;
        $countryId = $tableFilters['country_id'] ?? null;
        $currencyId = $tableFilters['currency_id'] ?? null;

        if (is_array($branchId)) {
            $branchId = !empty($branchId) ? (int) reset($branchId) : null;
        } else {
            $branchId = $branchId ? (int) $branchId : null;
        }

        if (is_array($countryId)) {
            $countryId = !empty($countryId) ? (int) reset($countryId) : null;
        } else {
            $countryId = $countryId ? (int) $countryId : null;
        }

        if (is_array($currencyId)) {
            $currencyId = !empty($currencyId) ? (int) reset($currencyId) : null;
        } else {
            $currencyId = $currencyId ? (int) $currencyId : null;
        }

        return [
            'from' => $dateFilter['from'] ?? now()->startOfMonth()->toDateString(),
            'to' => $dateFilter['to'] ?? now()->toDateString(),
            'branch_id' => $branchId,
            'country_id' => $countryId,
            'currency_id' => $currencyId,
            'status' => is_array($tableFilters['status'] ?? null) ? reset($tableFilters['status']) : ($tableFilters['status'] ?? null),
            'group_by' => is_array($tableFilters['group_by'] ?? null) ? reset($tableFilters['group_by']) : ($tableFilters['group_by'] ?? 'day'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        return auth()->user()?->can('finance_reports.view') ?? false;
    }
}
