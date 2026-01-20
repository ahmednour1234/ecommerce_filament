<?php

namespace App\Filament\Pages\Finance;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use App\Services\MainCore\CurrencyService;
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
        $defaultCurrencyId = app(CurrencyService::class)->defaultCurrency()?->id;

        $this->form->fill([
            'branch_id' => null,
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'currency_id' => $defaultCurrencyId,
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
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('finance_type_id', null);
                                $this->resetTable();
                            }),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('reports.branch_statement.filters.type', [], null, 'dashboard') ?: 'Type (Optional)')
                            ->options(function (callable $get) {
                                $query = FinanceType::query()->where('is_active', true);

                                $kind = $get('kind');
                                if ($kind) {
                                    $query->where('kind', $kind);
                                }

                                return $query->pluck('name_text', 'id');
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

    protected function getTableQuery(): Builder
    {
        $data = $this->data ?? [];

        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return BranchTransaction::query()->whereRaw('1 = 0');
        }

        return BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->when(!empty($data['kind']), function (Builder $q) use ($data) {
                $q->whereHas('financeType', fn ($x) => $x->where('kind', $data['kind']));
            })
            ->when(!empty($data['finance_type_id']), fn (Builder $q) => $q->where('finance_type_id', $data['finance_type_id']))
            ->with(['financeType', 'currency'])
            ->orderBy('trx_date')
            ->orderBy('id');
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
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => $this->exportToExcel($table, $this->getExportFilename('xlsx'))),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => $this->exportToPdf($table, $this->getExportFilename('pdf'))),
            ])
            ->paginated(false);
    }

    protected function getOpeningBalance(): float
    {
        $data = $this->data ?? [];

        if (empty($data['branch_id']) || empty($data['from']) || empty($data['currency_id'])) {
            return 0;
        }

        $income = (float) BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
            ->sum('amount');

        $expense = (float) BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
            ->sum('amount');

        return $income - $expense;
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $branch = Branch::find($this->data['branch_id'] ?? null);
        $sanitized = preg_replace('/[^a-z0-9]+/i', '_', $branch?->name ?? 'branch_statement');
        return strtolower($sanitized) . '_' . date('Y-m-d_His') . '.' . $extension;
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
