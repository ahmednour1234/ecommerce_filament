<?php

namespace App\Filament\Pages\Finance;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use App\Models\Finance\FinanceType;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TableExport;
use App\Exports\PdfExport;

class BranchStatementPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use ExportsTable;
    use FinanceModuleGate;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.finance.branch-statement';

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
                            ->reactive(),

                        Forms\Components\DatePicker::make('from')
                            ->label(tr('reports.branch_statement.filters.from', [], null, 'dashboard') ?: 'From Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('to')
                            ->label(tr('reports.branch_statement.filters.to', [], null, 'dashboard') ?: 'To Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('reports.branch_statement.filters.currency', [], null, 'dashboard') ?: 'Currency')
                            ->options(Currency::where('is_active', true)->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('kind')
                            ->label(tr('reports.branch_statement.filters.kind', [], null, 'dashboard') ?: 'Kind (Optional)')
                            ->options([
                                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('finance_type_id', null)),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('reports.branch_statement.filters.type', [], null, 'dashboard') ?: 'Type (Optional)')
                            ->options(function ($get) {
                                $query = \App\Models\Finance\FinanceType::query()->where('is_active', true);
                                if ($get('kind')) {
                                    $query->where('kind', $get('kind'));
                                }
                                return $query->get()->pluck('name_text', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn ($get) => $get('kind') !== null),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function getTableQuery(): Builder
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return BranchTransaction::query()->whereRaw('1 = 0');
        }

        $query = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->with(['financeType', 'currency']);

        if (!empty($data['kind'])) {
            $query->whereHas('financeType', function ($q) use ($data) {
                $q->where('kind', $data['kind']);
            });
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('finance_type_id', $data['finance_type_id']);
        }

        return $query->orderBy('trx_date')->orderBy('id');
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
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.kind')
                    ->label(tr('tables.branch_transactions.kind', [], null, 'dashboard') ?: 'Kind')
                    ->badge()
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'income' 
                        ? (tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income')
                        : (tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense')),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label(tr('tables.branch_transactions.type', [], null, 'dashboard') ?: 'Type')
                    ->getStateUsing(fn ($record) => $record->financeType?->name_text),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('tables.branch_transactions.reference_no', [], null, 'dashboard') ?: 'Reference')
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('tables.branch_transactions.recipient_name', [], null, 'dashboard') ?: 'Recipient'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(tr('tables.branch_transactions.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('forms.branch_transactions.notes', [], null, 'dashboard') ?: 'Notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->formatStateUsing(function ($state, $record) use (&$runningBalance) {
                        return number_format($runningBalance, 2);
                    })
                    ->alignEnd(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.branch_transactions.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('fields.status_pending', [], null, 'dashboard') ?: 'Pending',
                        'approved' => tr('fields.status_approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('fields.status_rejected', [], null, 'dashboard') ?: 'Rejected',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where('status', $data['value']);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('kind')
                    ->label(tr('tables.branch_transactions.filters.kind', [], null, 'dashboard') ?: 'Kind')
                    ->options([
                        'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                        'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('financeType', fn ($q) => $q->where('kind', $data['value']));
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('finance_type_id')
                    ->label(tr('tables.branch_transactions.filters.type', [], null, 'dashboard') ?: 'Type')
                    ->options(function () {
                        return FinanceType::where('is_active', true)
                            ->get()
                            ->pluck('name_text', 'id');
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () use ($table) {
                        return $this->exportToExcel($table, $this->getExportFilename('xlsx'));
                    }),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () use ($table) {
                        return $this->exportToPdf($table, $this->getExportFilename('pdf'));
                    }),
            ])
            ->paginated(false);
    }

    protected function getOpeningBalance(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['currency_id'])) {
            return 0;
        }

        $income = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
            ->sum('amount') ?? 0;

        $expense = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
            ->sum('amount') ?? 0;

        return (float) $income - (float) $expense;
    }

    protected function getTotalIncome(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return 0;
        }

        $query = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'));

        if (!empty($data['kind']) && $data['kind'] !== 'income') {
            return 0;
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('finance_type_id', $data['finance_type_id']);
        }

        return (float) ($query->sum('amount') ?? 0);
    }

    protected function getTotalExpense(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return 0;
        }

        $query = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'));

        if (!empty($data['kind']) && $data['kind'] !== 'expense') {
            return 0;
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('finance_type_id', $data['finance_type_id']);
        }

        return (float) ($query->sum('amount') ?? 0);
    }

    protected function getNetChange(): float
    {
        return $this->getTotalIncome() - $this->getTotalExpense();
    }

    protected function getClosingBalance(): float
    {
        return $this->getOpeningBalance() + $this->getNetChange();
    }

    protected function getExportTitle(): ?string
    {
        $branch = Branch::find($this->data['branch_id'] ?? null);
        $currency = Currency::find($this->data['currency_id'] ?? null);
        $from = $this->data['from'] ?? '';
        $to = $this->data['to'] ?? '';

        return 'Branch Statement - ' . ($branch?->name ?? '') . ' (' . $from . ' to ' . $to . ') - ' . ($currency?->code ?? '');
    }

    public function getTitle(): string
    {
        return tr('reports.branch_statement.title', [], null, 'dashboard') ?: 'Branch Statement';
    }

    public function getHeading(): string
    {
        return tr('reports.branch_statement.title', [], null, 'dashboard') ?: 'Branch Statement';
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
        return $user?->hasRole('super_admin') || $user?->can('finance.view_reports') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
