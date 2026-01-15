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
        $from = $dateFilter['from'] ? Carbon::parse($dateFilter['from'])->startOfDay() : now()->startOfMonth()->startOfDay();
        $to = $dateFilter['to'] ? Carbon::parse($dateFilter['to'])->endOfDay() : now()->endOfDay();
        $groupBy = $filters['group_by'] ?? 'day';

        $periodExpr = $groupBy === 'month'
            ? "DATE_FORMAT(branch_transactions.transaction_date, '%Y-%m')"
            : "DATE(branch_transactions.transaction_date)";

        $subQuery = DB::table('branch_transactions')
            ->whereBetween('transaction_date', [$from, $to])
            ->whereNull('deleted_at');

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $subQuery->where('branch_id', auth()->user()?->branch_id);
        }

        if (isset($filters['branch_id'])) {
            $subQuery->where('branch_id', $filters['branch_id']);
        }
        if (isset($filters['country_id'])) {
            $subQuery->where('country_id', $filters['country_id']);
        }
        if (isset($filters['currency_id'])) {
            $subQuery->where('currency_id', $filters['currency_id']);
        }
        if (isset($filters['status'])) {
            $subQuery->where('status', $filters['status']);
        }

        $unionQuery = $subQuery->selectRaw("
                {$periodExpr} as id,
                {$periodExpr} as period,
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense,
                (SUM(CASE WHEN type='income' THEN amount ELSE 0 END) - SUM(CASE WHEN type='expense' THEN amount ELSE 0 END)) as net
            ")
            ->groupByRaw($periodExpr);

        return BranchTransaction::query()
            ->withoutGlobalScopes()
            ->fromSub($unionQuery, 'report_data')
            ->select('report_data.*')
            ->orderBy('report_data.period', 'asc');
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
        return [
            'from' => $dateFilter['from'] ?? now()->startOfMonth()->toDateString(),
            'to' => $dateFilter['to'] ?? now()->toDateString(),
            'branch_id' => $tableFilters['branch_id'] ?? null,
            'country_id' => $tableFilters['country_id'] ?? null,
            'currency_id' => $tableFilters['currency_id'] ?? null,
            'status' => $tableFilters['status'] ?? null,
            'group_by' => $tableFilters['group_by'] ?? 'day',
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
