<?php

namespace App\Filament\Pages\Finance;

use App\Exports\ExpenseReportExcelExport;
use App\Exports\ReportPdfExport;
use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use ExportsTable;
    use FinanceModuleGate;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 52;
    protected static string $view = 'filament.pages.finance.expense-report';
    protected static ?string $navigationTranslationKey = 'sidebar.finance.reports.expense';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'date_from' => null,
            'date_to' => null,
            'branch_id' => null,
            'country_id' => null,
            'currency_id' => null,
            'payment_method' => null,
            'finance_type_id' => null,
            'q' => null,
        ];

        $this->form->fill($this->data);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('reports.filters.title', [], null, 'dashboard') ?: 'Filters')
                    ->schema([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('reports.expense.filters.date_from', [], null, 'dashboard') ?: 'From Date')
                            ->nullable()
                            ->reactive(),

                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('reports.expense.filters.date_to', [], null, 'dashboard') ?: 'To Date')
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('reports.expense.filters.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(fn () => Branch::where('status', 'active')
                                ->pluck('name', 'id')
                                ->map(fn ($v) => $this->ensureUtf8($v))
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('reports.expense.filters.country', [], null, 'dashboard') ?: 'Country')
                            ->options(fn () => Country::where('is_active', true)
                                ->pluck('name_text', 'id')
                                ->map(fn ($v) => $this->ensureUtf8($v))
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('reports.expense.filters.currency', [], null, 'dashboard') ?: 'Currency')
                            ->options(fn () => Currency::where('is_active', true)
                                ->pluck('code', 'id')
                                ->map(fn ($v) => $this->ensureUtf8($v))
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label(tr('reports.expense.filters.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('reports.expense.filters.category', [], null, 'dashboard') ?: 'Category')
                            ->options(fn () => FinanceType::where('kind', 'expense')
                                ->where('is_active', true)
                                ->pluck('name_text', 'id')
                                ->map(fn ($v) => $this->ensureUtf8($v))
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\TextInput::make('q')
                            ->label(tr('reports.expense.filters.search', [], null, 'dashboard') ?: 'Search')
                            ->placeholder(tr('reports.expense.filters.search_placeholder', [], null, 'dashboard') ?: 'Search by receiver, reference, notes...')
                            ->nullable()
                            ->reactive(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function baseQuery(): Builder
    {
        return BranchTransaction::query()
            ->whereHas('financeType', fn (Builder $q) => $q->where('kind', 'expense'))
            ->with(['financeType', 'branch', 'country', 'currency', 'creator']);
    }

    protected function applyFilters(Builder $query): void
    {
        $d = $this->form->getRawState();

        $from = !empty($d['date_from']) ? Carbon::parse($d['date_from'])->startOfDay() : null;
        $to = !empty($d['date_to']) ? Carbon::parse($d['date_to'])->endOfDay() : null;

        if ($from && $to) {
            $query->whereBetween('trx_date', [$from, $to]);
        } elseif ($from) {
            $query->where('trx_date', '>=', $from);
        } elseif ($to) {
            $query->where('trx_date', '<=', $to);
        }

        if (!empty($d['branch_id'])) $query->where('branch_id', $d['branch_id']);
        if (!empty($d['country_id'])) $query->where('country_id', $d['country_id']);
        if (!empty($d['currency_id'])) $query->where('currency_id', $d['currency_id']);

        if (!empty($d['payment_method'])) {
            $query->where('payment_method', 'like', '%' . $d['payment_method'] . '%');
        }

        if (!empty($d['finance_type_id'])) {
            $query->where('finance_type_id', $d['finance_type_id']);
        }

        if (!empty($d['q'])) {
            $search = $d['q'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('recipient_name', 'like', '%' . $search . '%')
                    ->orWhere('reference_no', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }
    }

    protected function getTableQuery(): Builder
    {
        $query = $this->baseQuery();
        $this->applyFilters($query);

        return $query->orderBy('trx_date', 'desc')->orderBy('id', 'desc');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('trx_date')
                    ->label(tr('reports.expense.columns.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('reports.expense.columns.branch', [], null, 'dashboard') ?: 'Branch')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->branch?->name ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name_text')
                    ->label(tr('reports.expense.columns.country', [], null, 'dashboard') ?: 'Country')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->country?->name_text ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('reports.expense.columns.currency', [], null, 'dashboard') ?: 'Currency')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->currency?->code ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label(tr('reports.expense.columns.category', [], null, 'dashboard') ?: 'Category')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->financeType?->name_text ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('reports.expense.columns.amount', [], null, 'dashboard') ?: 'Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(tr('reports.expense.columns.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->payment_method ?? ''))
                    ->searchable(),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('reports.expense.columns.reference', [], null, 'dashboard') ?: 'Reference')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->reference_no ?? ''))
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('reports.expense.columns.receiver', [], null, 'dashboard') ?: 'Receiver')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->recipient_name ?? ''))
                    ->searchable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('reports.expense.columns.created_by', [], null, 'dashboard') ?: 'Created By')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->creator?->name ?? ''))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('trx_date')
                    ->label(tr('reports.expense.filters.date_range', [], null, 'dashboard') ?: 'Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('reports.expense.filters.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('reports.expense.filters.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('trx_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('trx_date', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('reports.expense.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label(tr('reports.expense.filters.country', [], null, 'dashboard') ?: 'Country')
                    ->options(fn () => Country::where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn ($country) => [$country->id => $this->ensureUtf8($country->name_text)])
                        ->toArray())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label(tr('reports.expense.filters.currency', [], null, 'dashboard') ?: 'Currency')
                    ->relationship('currency', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('finance_type_id')
                    ->label(tr('reports.expense.filters.category', [], null, 'dashboard') ?: 'Category')
                    ->options(fn () => FinanceType::where('kind', 'expense')
                        ->where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn ($type) => [$type->id => $this->ensureUtf8($type->name_text)])
                        ->toArray())
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('payment_method')
                    ->label(tr('reports.expense.filters.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->form([
                        Forms\Components\TextInput::make('payment_method')
                            ->label(tr('reports.expense.filters.payment_method', [], null, 'dashboard') ?: 'Payment Method'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_method'],
                                fn (Builder $query, $value): Builder => $query->where('payment_method', 'like', '%' . $value . '%'),
                            );
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->searchable()
            ->searchPlaceholder(tr('reports.expense.filters.search_placeholder', [], null, 'dashboard') ?: 'Search by receiver, reference, notes...')
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => $this->exportToExcelExpense()),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => $this->exportToPdfExpense()),
            ])
            ->defaultSort('trx_date', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public function exportToExcelExpense()
    {
        $records = $this->getTableQuery()->get();

        $detailedData = $records->map(fn ($r) => [
            'date' => $r->trx_date?->format('Y-m-d') ?? '',
            'branch' => $this->ensureUtf8($r->branch?->name ?? ''),
            'country' => $this->ensureUtf8($r->country?->name_text ?? ''),
            'currency' => $this->ensureUtf8($r->currency?->code ?? ''),
            'category' => $this->ensureUtf8($r->financeType?->name_text ?? ''),
            'amount' => number_format((float) $r->amount, 2),
            'payment_method' => $this->ensureUtf8($r->payment_method ?? ''),
            'reference_no' => $this->ensureUtf8($r->reference_no ?? ''),
            'receiver' => $this->ensureUtf8($r->recipient_name ?? ''),
            'created_by' => $this->ensureUtf8($r->creator?->name ?? ''),
        ]);

        $summaryData = $this->getGroupedByCategory();

        $export = new ExpenseReportExcelExport($detailedData, $summaryData, $this->getExportTitle());
        return Excel::download($export, $this->getExportFilename('xlsx'));
    }

    public function exportToPdfExpense()
    {
        $records = $this->getTableQuery()->get();

        $detailedRows = $records->map(fn ($r) => [
            $r->trx_date?->format('Y-m-d') ?? '',
            $this->ensureUtf8($r->branch?->name ?? ''),
            $this->ensureUtf8($r->country?->name_text ?? ''),
            $this->ensureUtf8($r->currency?->code ?? ''),
            $this->ensureUtf8($r->financeType?->name_text ?? ''),
            number_format((float) $r->amount, 2),
            $this->ensureUtf8($r->payment_method ?? ''),
            $this->ensureUtf8($r->reference_no ?? ''),
            $this->ensureUtf8($r->recipient_name ?? ''),
            $this->ensureUtf8($r->creator?->name ?? ''),
        ])->toArray();

        $headers = [
            tr('reports.expense.columns.date', [], null, 'dashboard') ?: 'Date',
            tr('reports.expense.columns.branch', [], null, 'dashboard') ?: 'Branch',
            tr('reports.expense.columns.country', [], null, 'dashboard') ?: 'Country',
            tr('reports.expense.columns.currency', [], null, 'dashboard') ?: 'Currency',
            tr('reports.expense.columns.category', [], null, 'dashboard') ?: 'Category',
            tr('reports.expense.columns.amount', [], null, 'dashboard') ?: 'Amount',
            tr('reports.expense.columns.payment_method', [], null, 'dashboard') ?: 'Payment Method',
            tr('reports.expense.columns.reference', [], null, 'dashboard') ?: 'Reference',
            tr('reports.expense.columns.receiver', [], null, 'dashboard') ?: 'Receiver',
            tr('reports.expense.columns.created_by', [], null, 'dashboard') ?: 'Created By',
        ];

        $metadata = $this->getExportMetadata();

        $export = new ReportPdfExport(
            collect($detailedRows),
            $headers,
            tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report',
            $metadata,
            app()->getLocale() === 'ar',
            'reports.expense-report-pdf'
        );

        return $export->download($this->getExportFilename('pdf'));
    }

    public function getTotalExpenses(): float
    {
        $query = $this->baseQuery();
        $this->applyFilters($query);
        return (float) ($query->sum('amount') ?? 0);
    }

    public function getTransactionCount(): int
    {
        $query = $this->baseQuery();
        $this->applyFilters($query);
        return (int) $query->count();
    }

    public function getGroupedByCategory(): Collection
    {
        $query = $this->baseQuery();
        $this->applyFilters($query);

        return $query
            ->select('finance_type_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('finance_type_id')
            ->with('financeType:id,name')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($item) => [
                'category_name' => $this->ensureUtf8($item->financeType?->name_text ?? ''),
                'count' => (int) $item->count,
                'total_amount' => (float) $item->total_amount,
            ]);
    }

    protected function getExportTitle(): string
    {
        $base = tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report';
        $d = $this->form->getRawState();
        $from = $d['date_from'] ?? null;
        $to = $d['date_to'] ?? null;

        if (!$from && !$to) return $base;

        return $base . ' (' . ($from ?: '-') . ' to ' . ($to ?: '-') . ')';
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        return 'expense_report_' . now()->format('Y-m-d_His') . '.' . $extension;
    }

    protected function getExportMetadata(): array
    {
        $d = $this->form->getRawState();
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => Auth::user()?->name ?? 'System',
            'date_from' => $d['date_from'] ?? '',
            'date_to' => $d['date_to'] ?? '',
        ];

        if (!empty($d['branch_id'])) {
            $metadata['branch'] = $this->ensureUtf8(Branch::find($d['branch_id'])?->name ?? '');
        }

        if (!empty($d['currency_id'])) {
            $metadata['currency'] = $this->ensureUtf8(Currency::find($d['currency_id'])?->code ?? '');
        }

        return $metadata;
    }

    protected function ensureUtf8($value): string
    {
        if (is_null($value)) return '';
        if (is_numeric($value) || is_bool($value)) return (string) $value;
        $value = is_string($value) ? $value : (string) $value;
        return mb_check_encoding($value, 'UTF-8') ? $value : mb_convert_encoding($value, 'UTF-8', 'auto');
    }

    public function getTitle(): string
    {
        return tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report';
    }

    public function getHeading(): string
    {
        return tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report';
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('super_admin')
            || $user?->can('finance.reports.expense')
            || ($user?->can('finance_reports.view') ?? false);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
