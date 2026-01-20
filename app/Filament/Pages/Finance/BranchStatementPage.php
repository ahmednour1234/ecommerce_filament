<?php

namespace App\Filament\Pages\Finance;

use App\Filament\Concerns\ExportsTable;
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
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchStatementPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use ExportsTable;
    use FinanceModuleGate;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationLabel = null;
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.finance.branch-statement';

    public ?array $data = [];

    private const DEFAULT_CURRENCY_ID = 3;

    public static function getNavigationGroup(): ?string
    {
        return tr('navigation.groups.finance', [], null, 'dashboard') ?: 'Finance';
    }

    public static function getNavigationLabel(): string
    {
        return tr('navigation.bar.finance.branch_statement', [], null, 'dashboard') ?: 'Branch Statement';
    }

    public function getTitle(): string
    {
        return tr('reports.branch_statement.title', [], null, 'dashboard') ?: 'Branch Statement';
    }

    public function getHeading(): string
    {
        return tr('reports.branch_statement.title', [], null, 'dashboard') ?: 'Branch Statement';
    }

    public function mount(): void
    {
        $this->form->fill([
            'branch_id' => null,
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'currency_id' => self::DEFAULT_CURRENCY_ID,
            'kind' => null,
            'finance_type_id' => null,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('reports.filters.title', [], null, 'dashboard') ?: 'Filters')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('reports.branch_statement.filters.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('from')
                            ->label(tr('reports.branch_statement.filters.from', [], null, 'dashboard') ?: 'From Date')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('to')
                            ->label(tr('reports.branch_statement.filters.to', [], null, 'dashboard') ?: 'To Date')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('reports.branch_statement.filters.currency', [], null, 'dashboard') ?: 'Currency')
                            ->options(Currency::where('is_active', true)->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(self::DEFAULT_CURRENCY_ID)
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('kind')
                            ->label(tr('reports.branch_statement.filters.kind', [], null, 'dashboard') ?: 'Kind (Optional)')
                            ->options([
                                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                            ])
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('finance_type_id', null);
                                $this->resetTable();
                            }),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('reports.branch_statement.filters.type', [], null, 'dashboard') ?: 'Type (Optional)')
                            ->options(function (callable $get) {
                                $q = FinanceType::query()->where('is_active', true);
                                if ($get('kind')) {
                                    $q->where('kind', $get('kind'));
                                }
                                return $q->pluck('name_text', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (callable $get) => (bool) $get('kind'))
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function guardReady(): bool
    {
        $d = $this->data ?? [];
        return !empty($d['branch_id']) && !empty($d['from']) && !empty($d['to']) && !empty($d['currency_id']);
    }

    protected function getTableQuery(): Builder
    {
        if (!$this->guardReady()) {
            return BranchTransaction::query()->whereRaw('1 = 0');
        }

        $d = $this->data;

        $q = BranchTransaction::query()
            ->where('branch_id', $d['branch_id'])
            ->where('currency_id', $d['currency_id'])
            ->whereBetween('trx_date', [$d['from'], $d['to']])
            ->with(['financeType', 'currency']);

        if (!empty($d['kind'])) {
            $q->whereHas('financeType', fn ($x) => $x->where('kind', $d['kind']));
        }

        if (!empty($d['finance_type_id'])) {
            $q->where('finance_type_id', $d['finance_type_id']);
        }

        return $q->orderBy('trx_date')->orderBy('id');
    }

    public function table(Table $table): Table
    {
        $openingBalance = $this->getOpeningBalance();
        $runningBalance = $openingBalance;

        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('trx_date')
                    ->label(tr('tables.branch_transactions.trx_date', [], null, 'dashboard') ?: 'Date')
                    ->date(),

                Tables\Columns\TextColumn::make('financeType.kind')
                    ->label(tr('tables.branch_transactions.kind', [], null, 'dashboard') ?: 'Kind')
                    ->badge()
                    ->colors(['success' => 'income', 'danger' => 'expense'])
                    ->formatStateUsing(fn ($state) => $state === 'income'
                        ? (tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income')
                        : (tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense')),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label(tr('tables.branch_transactions.type', [], null, 'dashboard') ?: 'Type')
                    ->getStateUsing(fn ($record) => $record->financeType?->name_text),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('tables.branch_transactions.reference_no', [], null, 'dashboard') ?: 'Reference'),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('tables.branch_transactions.recipient_name', [], null, 'dashboard') ?: 'Recipient'),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('tables.branch_transactions.amount', [], null, 'dashboard') ?: 'Amount')
                    ->formatStateUsing(function ($state, $record) use (&$runningBalance) {
                        $amount = (float) $state;
                        if ($record->financeType?->kind === 'expense') {
                            $amount = -$amount;
                        }
                        $runningBalance += $amount;
                        return number_format($amount, 2);
                    })
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('running_balance')
                    ->label(tr('tables.branch_transactions.running_balance', [], null, 'dashboard') ?: 'Running Balance')
                    ->formatStateUsing(fn () => number_format($runningBalance, 2))
                    ->alignEnd(),
            ])
            ->paginated(false);
    }

    public function getOpeningBalance(): float
    {
        $d = $this->data ?? [];
        if (empty($d['branch_id']) || empty($d['from']) || empty($d['currency_id'])) {
            return 0;
        }

        $income = (float) BranchTransaction::query()
            ->where('branch_id', $d['branch_id'])
            ->where('currency_id', $d['currency_id'])
            ->where('trx_date', '<', $d['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
            ->sum('amount');

        $expense = (float) BranchTransaction::query()
            ->where('branch_id', $d['branch_id'])
            ->where('currency_id', $d['currency_id'])
            ->where('trx_date', '<', $d['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
            ->sum('amount');

        return $income - $expense;
    }

    public function getTotalIncome(): float
    {
        $d = $this->data ?? [];
        if (!$this->guardReady()) {
            return 0;
        }

        if (!empty($d['kind']) && $d['kind'] !== 'income') {
            return 0;
        }

        $q = BranchTransaction::query()
            ->where('branch_id', $d['branch_id'])
            ->where('currency_id', $d['currency_id'])
            ->whereBetween('trx_date', [$d['from'], $d['to']])
            ->whereHas('financeType', fn ($x) => $x->where('kind', 'income'));

        if (!empty($d['finance_type_id'])) {
            $q->where('finance_type_id', $d['finance_type_id']);
        }

        return (float) $q->sum('amount');
    }

    public function getTotalExpense(): float
    {
        $d = $this->data ?? [];
        if (!$this->guardReady()) {
            return 0;
        }

        if (!empty($d['kind']) && $d['kind'] !== 'expense') {
            return 0;
        }

        $q = BranchTransaction::query()
            ->where('branch_id', $d['branch_id'])
            ->where('currency_id', $d['currency_id'])
            ->whereBetween('trx_date', [$d['from'], $d['to']])
            ->whereHas('financeType', fn ($x) => $x->where('kind', 'expense'));

        if (!empty($d['finance_type_id'])) {
            $q->where('finance_type_id', $d['finance_type_id']);
        }

        return (float) $q->sum('amount');
    }

    public function getNetChange(): float
    {
        return $this->getTotalIncome() - $this->getTotalExpense();
    }

    public function getClosingBalance(): float
    {
        return $this->getOpeningBalance() + $this->getNetChange();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || ($user?->can('finance.view_reports') ?? false);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
