<?php

namespace App\Filament\Pages\Finance;

use App\Exports\PdfExport;
use App\Exports\TableExport;
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
use Illuminate\Support\Facades\DB;

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
            'from' => now()->startOfYear()->format('Y-m-d'),
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
                            ->options(fn () => Branch::where('status', 'active')->get()
                                ->mapWithKeys(fn ($b) => [$b->id => $this->ensureUtf8($b->name ?? '')])
                            )
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
                            ->options(fn () => Currency::where('is_active', true)->get()
                                ->mapWithKeys(fn ($c) => [$c->id => $this->ensureUtf8($c->code ?? '')])
                            )
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
                                $q = FinanceType::query()->where('is_active', true);
                                if ($get('kind')) {
                                    $q->where('kind', $get('kind'));
                                }
                                return $q->get()->mapWithKeys(fn ($t) => [$t->id => $this->ensureUtf8($t->name_text ?? '')]);
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

        if (
            empty($data['branch_id']) ||
            empty($data['from']) ||
            empty($data['to']) ||
            empty($data['currency_id'])
        ) {
            return BranchTransaction::query();
        }

        $opening = (float) $this->getOpeningBalance();

        $query = BranchTransaction::query()
            ->from('finance_branch_transactions as bt')
            ->join('finance_types as ft', 'ft.id', '=', 'bt.finance_type_id')
            ->where('bt.branch_id', $data['branch_id'])
            ->where('bt.currency_id', $data['currency_id'])
            ->whereBetween('bt.trx_date', [$data['from'], $data['to']]);

        if (!empty($data['kind'])) {
            $query->where('ft.kind', $data['kind']);
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('bt.finance_type_id', $data['finance_type_id']);
        }

        $signedAmountSql = "CASE WHEN ft.kind = 'expense' THEN -bt.amount ELSE bt.amount END";
        $runningBalanceSql = "(" . $opening . " + SUM($signedAmountSql) OVER (ORDER BY bt.trx_date, bt.id))";

        return $query
            ->select([
                'bt.*',
                DB::raw("$signedAmountSql as signed_amount"),
                DB::raw("$runningBalanceSql as running_balance"),
            ])
            ->with(['financeType', 'currency', 'branch'])
            ->orderBy('bt.trx_date')
            ->orderBy('bt.id');
    }

    public function table(Table $table): Table
    {
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
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->financeType?->name_text ?? '')),

                // ✅ المبلغ (موقعه بدري عشان يبان في RTL)
                Tables\Columns\TextColumn::make('signed_amount')
                    ->label(tr('tables.branch_transactions.amount', [], null, 'dashboard') ?: 'Amount')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2))
                    ->alignEnd(),

                // ✅ الرصيد الجاري (موقعه بدري عشان يبان في RTL)
                Tables\Columns\TextColumn::make('running_balance')
                    ->label(tr('tables.branch_transactions.running_balance', [], null, 'dashboard') ?: 'Running Balance')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2))
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('tables.branch_transactions.reference_no', [], null, 'dashboard') ?: 'Reference')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->reference_no ?? ''))
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('tables.branch_transactions.recipient_name', [], null, 'dashboard') ?: 'Recipient')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->recipient_name ?? '')),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(tr('tables.branch_transactions.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->payment_method ?? ''))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('forms.branch_transactions.notes', [], null, 'dashboard') ?: 'Notes')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->notes ?? ''))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.branch_transactions.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where('branch_id', $data['value']);
                        }
                        return $query;
                    }),

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
                    ->options(fn () => FinanceType::where('is_active', true)->get()
                        ->mapWithKeys(fn ($t) => [$t->id => $this->ensureUtf8($t->name_text ?? '')])
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => $this->exportToExcel($this->table, $this->getExportFilename('xlsx'))),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => $this->exportToPdf($this->table, $this->getExportFilename('pdf'))),
            ])
            ->paginated(false);
    }

    protected function getOpeningBalance(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['currency_id'])) {
            return 0;
        }

        $income = (float) (BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
            ->sum('amount') ?? 0);

        $expense = (float) (BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
            ->sum('amount') ?? 0);

        return $income - $expense;
    }

    protected function getTotalIncome(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return 0;
        }

        if (!empty($data['kind']) && $data['kind'] !== 'income') {
            return 0;
        }

        $q = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->whereHas('financeType', fn ($x) => $x->where('kind', 'income'));

        if (!empty($data['finance_type_id'])) {
            $q->where('finance_type_id', $data['finance_type_id']);
        }

        return (float) ($q->sum('amount') ?? 0);
    }

    protected function getTotalExpense(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return 0;
        }

        if (!empty($data['kind']) && $data['kind'] !== 'expense') {
            return 0;
        }

        $q = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->whereHas('financeType', fn ($x) => $x->where('kind', 'expense'));

        if (!empty($data['finance_type_id'])) {
            $q->where('finance_type_id', $data['finance_type_id']);
        }

        return (float) ($q->sum('amount') ?? 0);
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

        $branchName = $this->ensureUtf8($branch?->name ?? '');
        $currencyCode = $this->ensureUtf8($currency?->code ?? '');

        return 'Branch Statement - ' . $branchName . ' (' . $from . ' to ' . $to . ') - ' . $currencyCode;
    }

    protected function ensureUtf8($value): string
    {
        if (is_null($value)) return '';
        if (is_numeric($value) || is_bool($value)) return (string) $value;
        if (!is_string($value)) $value = (string) $value;

        if (mb_check_encoding($value, 'UTF-8')) return $value;

        $detected = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1256', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) return $converted;
        }

        if (function_exists('iconv')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($cleaned !== false) return $cleaned;
        }

        $cleaned = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        if (mb_check_encoding($cleaned, 'UTF-8')) return $cleaned;

        return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) ?: '';
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
        $branchName = $this->ensureUtf8($branch?->name ?? 'branch_statement');
        $sanitized = preg_replace('/[^a-z0-9]+/i', '_', $branchName);
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
