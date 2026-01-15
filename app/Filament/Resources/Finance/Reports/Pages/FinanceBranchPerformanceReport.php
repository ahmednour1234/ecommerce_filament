<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\Reports\Widgets\TopBranchesNetBarChart;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class FinanceBranchPerformanceReport extends Page implements HasForms, HasTable
{
    use TranslatableNavigation;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 51;

    protected static ?string $title = 'Finance Reports - Branch Performance';
    protected static ?string $navigationLabel = 'Reports (Branches)';

    protected static string $view = 'filament.finance.reports.branch-performance-report';

    public function mount(): void
    {
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
            ])
            ->defaultSort('net', 'desc')
            ->paginated(false);
    }

    protected function reportQuery(): Builder
    {
        $filters = $this->tableFilters ?? [];
        $dateFilter = $filters['transaction_date'] ?? [];
        $from = isset($dateFilter['from']) && $dateFilter['from'] ? Carbon::parse($dateFilter['from'])->startOfDay() : now()->startOfMonth()->startOfDay();
        $to = isset($dateFilter['to']) && $dateFilter['to'] ? Carbon::parse($dateFilter['to'])->endOfDay() : now()->endOfDay();

        $q = BranchTransaction::query()
            ->join('branches', 'branch_transactions.branch_id', '=', 'branches.id')
            ->whereBetween('branch_transactions.transaction_date', [$from, $to]);

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
        $tableFilters = $this->tableFilters ?? [];
        $dateFilter = $tableFilters['transaction_date'] ?? [];
        return [
            'from' => $dateFilter['from'] ?? now()->startOfMonth()->toDateString(),
            'to' => $dateFilter['to'] ?? now()->toDateString(),
            'country_id' => $tableFilters['country_id'] ?? null,
            'currency_id' => $tableFilters['currency_id'] ?? null,
            'status' => $tableFilters['status'] ?? null,
        ];
    }

    public function kpis(): array
    {
        $from = now()->startOfMonth()->startOfDay();
        $to = now()->endOfDay();

        $q = BranchTransaction::query()
            ->whereBetween('transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        $tableFilters = $this->tableFilters ?? [];
        $q->when($tableFilters['country_id'] ?? null, fn($qq, $id) => $qq->where('country_id', $id));
        $q->when($tableFilters['currency_id'] ?? null, fn($qq, $id) => $qq->where('currency_id', $id));
        $q->when($tableFilters['status'] ?? null, fn($qq, $status) => $qq->where('status', $status));

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
