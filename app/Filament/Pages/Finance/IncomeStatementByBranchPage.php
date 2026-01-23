<?php

namespace App\Filament\Pages\Finance;

use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
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
    use TranslatableNavigation;

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
            'kind' => null,
            'finance_type_id' => null,
            'status' => null,
            'payment_method' => null,
            'country_id' => null,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('reports.filters.title', [], null, 'dashboard') ?: 'Filters')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('reports.income_statement.filters.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('from')
                            ->label(tr('reports.income_statement.filters.from', [], null, 'dashboard') ?: 'From Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('to')
                            ->label(tr('reports.income_statement.filters.to', [], null, 'dashboard') ?: 'To Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('reports.income_statement.filters.currency', [], null, 'dashboard') ?: 'Currency')
                            ->options(Currency::where('is_active', true)->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('kind')
                            ->label(tr('reports.income_statement.filters.kind', [], null, 'dashboard') ?: 'Kind (Optional)')
                            ->options([
                                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('finance_type_id', null)),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('reports.income_statement.filters.type', [], null, 'dashboard') ?: 'Type (Optional)')
                            ->options(function ($get) {
                                $query = FinanceType::query()->where('is_active', true);
                                if ($get('kind')) {
                                    $query->where('kind', $get('kind'));
                                }
                                return $query->get()->pluck('name_text', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn ($get) => $get('kind') !== null),

                        Forms\Components\Select::make('status')
                            ->label(tr('reports.income_statement.filters.status', [], null, 'dashboard') ?: 'Status (Optional)')
                            ->options([
                                'pending' => tr('forms.status.pending', [], null, 'dashboard') ?: 'Pending',
                                'approved' => tr('forms.status.approved', [], null, 'dashboard') ?: 'Approved',
                                'rejected' => tr('forms.status.rejected', [], null, 'dashboard') ?: 'Rejected',
                            ])
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('payment_method')
                            ->label(tr('reports.income_statement.filters.payment_method', [], null, 'dashboard') ?: 'Payment Method (Optional)')
                            ->options([
                                'cash' => tr('forms.payment_methods.cash', [], null, 'dashboard') ?: 'Cash',
                                'bank_transfer' => tr('forms.payment_methods.bank_transfer', [], null, 'dashboard') ?: 'Bank Transfer',
                                'cheque' => tr('forms.payment_methods.cheque', [], null, 'dashboard') ?: 'Cheque',
                                'card' => tr('forms.payment_methods.card', [], null, 'dashboard') ?: 'Card',
                                'other' => tr('forms.payment_methods.other', [], null, 'dashboard') ?: 'Other',
                            ])
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('reports.income_statement.filters.country', [], null, 'dashboard') ?: 'Country (Optional)')
                            ->options(\App\Models\MainCore\Country::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function getIncomeTypes(): array
    {
        $data = $this->data;
        if (empty($data['branch_id'])) {
            return [];
        }

        $types = FinanceType::where('kind', 'income')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        return $types->map(function ($type) use ($data) {
            $query = BranchTransaction::query()
                ->where('branch_id', $data['branch_id'])
                ->where('currency_id', $data['currency_id'])
                ->where('finance_type_id', $type->id)
                ->whereBetween('trx_date', [$data['from'], $data['to']]);

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            if (!empty($data['payment_method'])) {
                $query->where('payment_method', $data['payment_method']);
            }

            if (!empty($data['country_id'])) {
                $query->where('country_id', $data['country_id']);
            }

            if (!empty($data['kind']) && $data['kind'] !== 'income') {
                return [
                    'id' => $type->id,
                    'name' => $type->name_text,
                    'total' => 0.0,
                ];
            }

            if (!empty($data['finance_type_id']) && $data['finance_type_id'] != $type->id) {
                return [
                    'id' => $type->id,
                    'name' => $type->name_text,
                    'total' => 0.0,
                ];
            }

            $total = $query->sum('amount') ?? 0;

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
        if (empty($data['branch_id'])) {
            return [];
        }

        $types = FinanceType::where('kind', 'expense')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        return $types->map(function ($type) use ($data) {
            $query = BranchTransaction::query()
                ->where('branch_id', $data['branch_id'])
                ->where('currency_id', $data['currency_id'])
                ->where('finance_type_id', $type->id)
                ->whereBetween('trx_date', [$data['from'], $data['to']]);

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            if (!empty($data['payment_method'])) {
                $query->where('payment_method', $data['payment_method']);
            }

            if (!empty($data['country_id'])) {
                $query->where('country_id', $data['country_id']);
            }

            if (!empty($data['kind']) && $data['kind'] !== 'expense') {
                return [
                    'id' => $type->id,
                    'name' => $type->name_text,
                    'total' => 0.0,
                ];
            }

            if (!empty($data['finance_type_id']) && $data['finance_type_id'] != $type->id) {
                return [
                    'id' => $type->id,
                    'name' => $type->name_text,
                    'total' => 0.0,
                ];
            }

            $total = $query->sum('amount') ?? 0;

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

    public function getTitle(): string
    {
        return tr('reports.income_statement.title', [], null, 'dashboard') ?: 'Income Statement by Branch';
    }

    public function getHeading(): string
    {
        return tr('reports.income_statement.title', [], null, 'dashboard') ?: 'Income Statement by Branch';
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
