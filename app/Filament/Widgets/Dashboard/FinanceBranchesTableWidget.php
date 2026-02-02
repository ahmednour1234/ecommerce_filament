<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class FinanceBranchesTableWidget extends BaseWidget implements HasForms
{
    use InteractsWithForms;

    public ?int $branch_id = null;
    public ?int $finance_type_id = null;

    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'ملخص المالية حسب الفروع';

    public function mount(): void
    {
        $user = Auth::user();

        $this->branch_id = session()->get('dashboard_finance_branch_id') ?? ($user?->branch_id);
        $this->finance_type_id = session()->get('dashboard_finance_type_id');

        $this->form->fill([
            'branch_id' => $this->branch_id,
            'finance_type_id' => $this->finance_type_id,
        ]);
    }

    protected function hasForm(): bool
    {
        return true;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('فلاتر المالية')
                ->schema([
                    Select::make('branch_id')
                        ->label('الفرع')
                        ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->branch_id = $state ? (int) $state : null;
                            session()->put('dashboard_finance_branch_id', $this->branch_id);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),

                    Select::make('finance_type_id')
                        ->label('نوع المالية')
                        ->options(fn () => \App\Models\Finance\FinanceType::where('is_active', true)->get()->pluck('name_text', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->finance_type_id = $state ? (int) $state : null;
                            session()->put('dashboard_finance_type_id', $this->finance_type_id);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(true),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'filters';
    }

    protected function flushCache(): void
    {
        $dateRange = session()->get('dashboard_date_range', 'year');
        $dateFrom = session()->get('dashboard_date_from');
        $dateTo = session()->get('dashboard_date_to');

        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($dateRange === 'year') {
            $from = now()->startOfYear()->startOfDay();
            $to = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfYear()->startOfDay();
            $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }

        $user = Auth::user();
        $branchId = $this->branch_id ?? session()->get('dashboard_finance_branch_id') ?? ($user?->branch_id) ?? null;
        $financeTypeId = $this->finance_type_id ?? session()->get('dashboard_finance_type_id') ?? null;

        $cacheKey = "dashboard_finance_branches_table_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";
        Cache::forget($cacheKey);
    }

    public function table(Table $table): Table
    {
        $dateRange = session()->get('dashboard_date_range', 'year');
        $dateFrom = session()->get('dashboard_date_from');
        $dateTo = session()->get('dashboard_date_to');

        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($dateRange === 'year') {
            $from = now()->startOfYear()->startOfDay();
            $to = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfYear()->startOfDay();
            $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }

        $branchId = $this->branch_id ?? session('dashboard_finance_branch_id') ?? auth()->user()?->branch_id;
        $financeTypeId = $this->finance_type_id ?? session('dashboard_finance_type_id');

        $cacheKey = "dashboard_finance_branches_table_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";

        $branchesData = Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId, $financeTypeId) {
            $user = Auth::user();
            
            $branches = Branch::where('status', 'active')->get();
            
            if ($branchId) {
                $branches = $branches->where('id', $branchId);
            }

            if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
                if (method_exists($user, 'branches')) {
                    $branchIds = $user->branches()->pluck('branches.id')->toArray();
                    if (!empty($branchIds)) {
                        $branches = $branches->whereIn('id', $branchIds);
                    } else {
                        $branches = collect();
                    }
                } elseif ($user->branch_id) {
                    $branches = $branches->where('id', $user->branch_id);
                }
            }

            $rows = [];
            $totalIncome = 0;
            $totalExpense = 0;

            foreach ($branches as $branch) {
                $query = BranchTransaction::query()
                    ->where('branch_id', $branch->id)
                    ->whereBetween('trx_date', [$from, $to])
                    ->where('status', 'approved');

                if ($financeTypeId) {
                    $query->where('finance_type_id', $financeTypeId);
                }

                $incomeQuery = (clone $query)->income();
                $expenseQuery = (clone $query)->expense();

                $income = (float) $incomeQuery->sum('amount');
                $expense = (float) $expenseQuery->sum('amount');
                $net = $income - $expense;

                $totalIncome += $income;
                $totalExpense += $expense;

                $rows[] = [
                    'id' => 'branch_' . $branch->id,
                    'branch_name' => $branch->name,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $net,
                ];
            }

            $rows[] = [
                'id' => 'total',
                'branch_name' => 'صافي الربح',
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'net' => $totalIncome - $totalExpense,
            ];

            return $rows;
        });

        $unionQueries = [];
        foreach ($branchesData as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as branch_name, ? as income, ? as expense, ? as net', [
                $row['id'],
                $row['branch_name'],
                $row['income'],
                $row['expense'],
                $row['net'],
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw("'empty' as id, NULL as branch_name, 0 as income, 0 as expense, 0 as net");
        }

        return $table
            ->query(fn () => BranchTransaction::query()
                ->fromSub($unionQuery, 'branches_finance_data')
                ->select('branches_finance_data.*')
            )
            ->columns([
                TextColumn::make('branch_name')
                    ->label('بيان')
                    ->searchable()
                    ->weight(fn ($record) => $record->id === 'total' ? 'bold' : 'normal')
                    ->color(fn ($record) => $record->id === 'total' ? 'danger' : null),
                TextColumn::make('income')
                    ->label('الايراد')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($state) => Number::format($state, 2))
                    ->alignEnd()
                    ->weight(fn ($record) => $record->id === 'total' ? 'bold' : 'normal')
                    ->color(fn ($record) => $record->id === 'total' ? 'danger' : null),
                TextColumn::make('expense')
                    ->label('المصروف')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($state) => Number::format($state, 2))
                    ->alignEnd()
                    ->weight(fn ($record) => $record->id === 'total' ? 'bold' : 'normal')
                    ->color(fn ($record) => $record->id === 'total' ? 'danger' : null),
                TextColumn::make('net')
                    ->label('الصافي')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($state) => Number::format($state, 2))
                    ->alignEnd()
                    ->color(function ($record) {
                        if ($record->id === 'total') {
                            return 'danger';
                        }
                        return $record->net >= 0 ? 'success' : 'danger';
                    })
                    ->weight(fn ($record) => $record->id === 'total' ? 'bold' : 'normal'),
            ])
            ->defaultSort('branch_name')
            ->paginated(false);
    }

    public function getTableRecordKey($record): string
    {
        return (string) ($record->id ?? uniqid());
    }
}
