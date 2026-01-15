<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\Reports\Concerns\HasFinanceReportFilters;
use App\Filament\Resources\Finance\Reports\Widgets\IncomeExpenseDonutChart;
use App\Filament\Resources\Finance\Reports\Widgets\IncomeExpenseTrendChart;
use App\Models\Finance\BranchTransaction;
use Filament\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class IncomeExpenseReport extends Page implements HasForms, HasTable
{
    use TranslatableNavigation;
    use InteractsWithForms;
    use InteractsWithTable;
    use HasFinanceReportFilters;
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

        $this->initDefaultDates();
        $this->form->fill([
            'from' => $this->from,
            'to' => $this->to,
            'group_by' => $this->group_by,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $this->filtersForm($form);
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
                    ->formatStateUsing(fn ($s) => number_format((float) $s, 2))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('expense')
                    ->label(tr('reports.columns.expense', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($s) => number_format((float) $s, 2))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('net')
                    ->label(tr('reports.columns.net', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($s) => number_format((float) $s, 2))
                    ->sortable(),
            ])
            ->paginated(false);
    }

    protected function reportQuery(): Builder
    {
        [$from, $to] = $this->dateRange();

        $periodExpr = $this->group_by === 'month'
            ? "DATE_FORMAT(branch_transactions.transaction_date, '%Y-%m')"
            : "DATE(branch_transactions.transaction_date)";

        $subQuery = DB::table('branch_transactions')
            ->whereBetween('transaction_date', [$from, $to])
            ->whereNull('deleted_at');

        // permissions scope
        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $subQuery->where('branch_id', auth()->user()?->branch_id);
        }

        if ($this->branch_id) {
            $subQuery->where('branch_id', $this->branch_id);
        }
        if ($this->country_id) {
            $subQuery->where('country_id', $this->country_id);
        }
        if ($this->currency_id) {
            $subQuery->where('currency_id', $this->currency_id);
        }
        if ($this->status) {
            $subQuery->where('status', $this->status);
        }

        $unionQuery = $subQuery->selectRaw("
                {$periodExpr} as id,
                {$periodExpr} as period,
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense,
                (SUM(CASE WHEN type='income' THEN amount ELSE 0 END) - SUM(CASE WHEN type='expense' THEN amount ELSE 0 END)) as net
            ")
            ->groupByRaw($periodExpr)
            ->orderByRaw($periodExpr);

        return BranchTransaction::query()
            ->fromSub($unionQuery, 'report_data')
            ->select('report_data.*');
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
        return [
            'from' => $this->from,
            'to' => $this->to,
            'branch_id' => $this->branch_id,
            'country_id' => $this->country_id,
            'currency_id' => $this->currency_id,
            'status' => $this->status,
            'group_by' => $this->group_by,
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
