<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\Reports\Concerns\HasFinanceReportFilters;
use App\Filament\Resources\Finance\Reports\Widgets\TopBranchesNetBarChart;
use App\Models\Finance\BranchTransaction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceBranchPerformanceReport extends Page implements HasForms, HasTable
{
    use TranslatableNavigation;
    use InteractsWithForms;
    use InteractsWithTable;
    use HasFinanceReportFilters;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 51;

    protected static ?string $title = 'Finance Reports - Branch Performance';
    protected static ?string $navigationLabel = 'Reports (Branches)';

    protected static string $view = 'filament.finance.reports.branch-performance-report';

    public function mount(): void
    {
        $this->initDefaultDates();
        $this->form->fill([
            'from' => $this->from,
            'to'   => $this->to,
            'group_by' => $this->group_by,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $this->filtersForm($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->reportQuery())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('branch_name')
                    ->label(tr('reports.columns.branch', [], null, 'dashboard'))
                    ->sortable()
                    ->searchable(),

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
            ->defaultSort('net', 'desc')
            ->paginated(false);
    }

    protected function reportQuery(): Builder
    {
        [$from, $to] = $this->dateRange();

        $q = BranchTransaction::query()
            ->join('branches', 'branch_transactions.branch_id', '=', 'branches.id')
            ->whereBetween('branch_transactions.transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_transactions.branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->branch_id, fn ($qq) => $qq->where('branch_transactions.branch_id', $this->branch_id));
        $q->when($this->country_id, fn ($qq) => $qq->where('branch_transactions.country_id', $this->country_id));
        $q->when($this->currency_id, fn ($qq) => $qq->where('branch_transactions.currency_id', $this->currency_id));
        $q->when($this->status, fn ($qq) => $qq->where('branch_transactions.status', $this->status));

        return $q->selectRaw("
                branch_transactions.branch_id as id,
                branch_transactions.branch_id,
                branches.name as branch_name,
                SUM(CASE WHEN branch_transactions.type='income' THEN branch_transactions.amount ELSE 0 END) as income,
                SUM(CASE WHEN branch_transactions.type='expense' THEN branch_transactions.amount ELSE 0 END) as expense,
                (SUM(CASE WHEN branch_transactions.type='income' THEN branch_transactions.amount ELSE 0 END) - SUM(CASE WHEN branch_transactions.type='expense' THEN branch_transactions.amount ELSE 0 END)) as net
            ")
            ->groupBy('branch_transactions.branch_id', 'branches.name')
            ->orderByDesc('net');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TopBranchesNetBarChart::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'country_id' => $this->country_id,
            'currency_id' => $this->currency_id,
            'status' => $this->status,
        ];
    }

    public function kpis(): array
    {
        [$from, $to] = $this->dateRange();

        $q = BranchTransaction::query()
            ->whereBetween('transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));
        $q->when($this->status, fn($qq) => $qq->where('status', $this->status));

        $branchesCount = (clone $q)->distinct('branch_id')->count('branch_id');
        $txCount = (clone $q)->count();

        return [
            'branches' => $branchesCount,
            'tx' => $txCount,
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
