<?php

namespace App\Filament\Pages\Finance;

use App\Filament\Concerns\FinanceModuleGate;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class IncomeStatementByBranchPage extends Page implements HasForms
{
    use InteractsWithForms;
    use FinanceModuleGate;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 11;
    protected static string $view = 'filament.pages.finance.income-statement-by-branch';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'branch_id' => null,
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'currency_id' => null,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filters')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('to')
                            ->label('To Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('currency_id')
                            ->label('Currency')
                            ->options(Currency::where('is_active', true)->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),
                    ])
                    ->columns(4),
            ])
            ->statePath('data');
    }

    protected function getIncomeTypes(): array
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return [];
        }

        $types = FinanceType::where('kind', 'income')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        return $types->map(function ($type) use ($data) {
            $total = BranchTransaction::query()
                ->where('branch_id', $data['branch_id'])
                ->where('currency_id', $data['currency_id'])
                ->where('finance_type_id', $type->id)
                ->whereBetween('trx_date', [$data['from'], $data['to']])
                ->sum('amount') ?? 0;

            return [
                'id' => $type->id,
                'name' => $type->name_text,
                'total' => (float) $total,
            ];
        })->toArray();
    }

    protected function getExpenseTypes(): array
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return [];
        }

        $types = FinanceType::where('kind', 'expense')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        return $types->map(function ($type) use ($data) {
            $total = BranchTransaction::query()
                ->where('branch_id', $data['branch_id'])
                ->where('currency_id', $data['currency_id'])
                ->where('finance_type_id', $type->id)
                ->whereBetween('trx_date', [$data['from'], $data['to']])
                ->sum('amount') ?? 0;

            return [
                'id' => $type->id,
                'name' => $type->name_text,
                'total' => (float) $total,
            ];
        })->toArray();
    }

    protected function getTotalIncome(): float
    {
        $incomeTypes = $this->getIncomeTypes();
        return array_sum(array_column($incomeTypes, 'total'));
    }

    protected function getTotalExpense(): float
    {
        $expenseTypes = $this->getExpenseTypes();
        return array_sum(array_column($expenseTypes, 'total'));
    }

    protected function getNetProfit(): float
    {
        return $this->getTotalIncome() - $this->getTotalExpense();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.view_reports') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
