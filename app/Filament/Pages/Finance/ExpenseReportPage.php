<?php

namespace App\Filament\Pages\Finance;

use App\Exports\ExpenseReportExcelExport;
use App\Exports\ReportPdfExport;
use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
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
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'branch_id' => null,
            'country_id' => null,
            'currency_id' => null,
            'payment_method' => null,
            'finance_type_id' => null,
            'q' => null,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('reports.filters.title', [], null, 'dashboard') ?: 'Filters')
                    ->schema([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('reports.expense.filters.date_from', [], null, 'dashboard') ?: 'From Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('reports.expense.filters.date_to', [], null, 'dashboard') ?: 'To Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('reports.expense.filters.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(function () {
                                return Branch::where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(function ($branch) {
                                        return [$branch->id => $this->ensureUtf8($branch->name ?? '')];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('reports.expense.filters.country', [], null, 'dashboard') ?: 'Country')
                            ->options(function () {
                                return Country::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function ($country) {
                                        return [$country->id => $this->ensureUtf8($country->name_text ?? '')];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('reports.expense.filters.currency', [], null, 'dashboard') ?: 'Currency')
                            ->options(function () {
                                return Currency::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function ($currency) {
                                        return [$currency->id => $this->ensureUtf8($currency->code ?? '')];
                                    });
                            })
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
                            ->options(function () {
                                return FinanceType::where('kind', 'expense')
                                    ->where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function ($type) {
                                        return [$type->id => $this->ensureUtf8($type->name_text ?? '')];
                                    });
                            })
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

    protected function getTableQuery(): Builder
    {
        $data = $this->data;
        if (empty($data['date_from']) || empty($data['date_to'])) {
            return BranchTransaction::query()->whereRaw('1 = 0');
        }

        $query = BranchTransaction::query()
            ->whereHas('financeType', fn($q) => $q->where('kind', 'expense'))
            ->whereBetween('trx_date', [$data['date_from'], $data['date_to']])
            ->with(['financeType', 'branch', 'country', 'currency', 'creator']);

        if (!empty($data['branch_id'])) {
            $query->where('branch_id', $data['branch_id']);
        }

        if (!empty($data['country_id'])) {
            $query->where('country_id', $data['country_id']);
        }

        if (!empty($data['currency_id'])) {
            $query->where('currency_id', $data['currency_id']);
        }

        if (!empty($data['payment_method'])) {
            $query->where('payment_method', 'like', '%' . $data['payment_method'] . '%');
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('finance_type_id', $data['finance_type_id']);
        }

        if (!empty($data['q'])) {
            $search = $data['q'];
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', '%' . $search . '%')
                    ->orWhere('reference_no', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('trx_date', 'desc')->orderBy('id', 'desc');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('trx_date')
                    ->label(tr('reports.expense.columns.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('reports.expense.columns.branch', [], null, 'dashboard') ?: 'Branch')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->branch?->name ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name_text')
                    ->label(tr('reports.expense.columns.country', [], null, 'dashboard') ?: 'Country')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->country?->name_text ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('reports.expense.columns.currency', [], null, 'dashboard') ?: 'Currency')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->currency?->code ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label(tr('reports.expense.columns.category', [], null, 'dashboard') ?: 'Category')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->financeType?->name_text ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('reports.expense.columns.amount', [], null, 'dashboard') ?: 'Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(tr('reports.expense.columns.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->payment_method ?? ''))
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('reports.expense.columns.reference', [], null, 'dashboard') ?: 'Reference')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->reference_no ?? ''))
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('reports.expense.columns.receiver', [], null, 'dashboard') ?: 'Receiver')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->recipient_name ?? ''))
                    ->searchable(),

                Tables\Columns\IconColumn::make('attachment_path')
                    ->label(tr('reports.expense.columns.attachment', [], null, 'dashboard') ?: 'Attachment')
                    ->icon(fn($record) => $record->attachment_path ? 'heroicon-o-paper-clip' : null)
                    ->url(fn($record) => $record->attachment_path ? asset('storage/' . $record->attachment_path) : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('reports.expense.columns.created_by', [], null, 'dashboard') ?: 'Created By')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->creator?->name ?? ''))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('reports.expense.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(function () {
                        return Branch::where('status', 'active')
                            ->get()
                            ->mapWithKeys(function ($branch) {
                                return [$branch->id => $this->ensureUtf8($branch->name ?? '')];
                            });
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('finance_type_id')
                    ->label(tr('reports.expense.filters.category', [], null, 'dashboard') ?: 'Category')
                    ->options(function () {
                        return FinanceType::where('kind', 'expense')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($type) {
                                return [$type->id => $this->ensureUtf8($type->name_text ?? '')];
                            });
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return $this->exportToExcelExpense();
                    }),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return $this->exportToPdfExpense();
                    }),
            ])
            ->defaultSort('trx_date', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public function getTotalExpenses(): float
    {
        $data = $this->data;
        if (empty($data['date_from']) || empty($data['date_to'])) {
            return 0;
        }

        $query = BranchTransaction::query()
            ->whereHas('financeType', fn($q) => $q->where('kind', 'expense'))
            ->whereBetween('trx_date', [$data['date_from'], $data['date_to']]);

        $this->applyFiltersToQuery($query);

        return (float) ($query->sum('amount') ?? 0);
    }

    public function getTransactionCount(): int
    {
        $data = $this->data;
        if (empty($data['date_from']) || empty($data['date_to'])) {
            return 0;
        }

        $query = BranchTransaction::query()
            ->whereHas('financeType', fn($q) => $q->where('kind', 'expense'))
            ->whereBetween('trx_date', [$data['date_from'], $data['date_to']]);

        $this->applyFiltersToQuery($query);

        return (int) $query->count();
    }

    public function getGroupedByCategory(): Collection
    {
        $data = $this->data;
        if (empty($data['date_from']) || empty($data['date_to'])) {
            return collect([]);
        }

        $query = BranchTransaction::query()
            ->whereHas('financeType', fn($q) => $q->where('kind', 'expense'))
            ->whereBetween('trx_date', [$data['date_from'], $data['date_to']]);

        $this->applyFiltersToQuery($query);

        return $query
            ->select('finance_type_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('finance_type_id')
            ->with('financeType')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $this->ensureUtf8($item->financeType?->name_text ?? ''),
                    'count' => $item->count,
                    'total_amount' => (float) $item->total_amount,
                ];
            });
    }

    protected function applyFiltersToQuery(Builder $query): void
    {
        $data = $this->data;

        if (!empty($data['branch_id'])) {
            $query->where('branch_id', $data['branch_id']);
        }

        if (!empty($data['country_id'])) {
            $query->where('country_id', $data['country_id']);
        }

        if (!empty($data['currency_id'])) {
            $query->where('currency_id', $data['currency_id']);
        }

        if (!empty($data['payment_method'])) {
            $query->where('payment_method', 'like', '%' . $data['payment_method'] . '%');
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('finance_type_id', $data['finance_type_id']);
        }

        if (!empty($data['q'])) {
            $search = $data['q'];
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', '%' . $search . '%')
                    ->orWhere('reference_no', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }
    }

    public function exportToExcelExpense()
    {
        $data = $this->data;
        $tableQuery = $this->getTableQuery();
        $records = $tableQuery->get();

        $detailedData = $records->map(function ($record) {
            return [
                'date' => $record->trx_date?->format('Y-m-d') ?? '',
                'branch' => $this->ensureUtf8($record->branch?->name ?? ''),
                'country' => $this->ensureUtf8($record->country?->name_text ?? ''),
                'currency' => $this->ensureUtf8($record->currency?->code ?? ''),
                'category' => $this->ensureUtf8($record->financeType?->name_text ?? ''),
                'amount' => number_format((float) $record->amount, 2),
                'payment_method' => $this->ensureUtf8($record->payment_method ?? ''),
                'reference_no' => $this->ensureUtf8($record->reference_no ?? ''),
                'receiver' => $this->ensureUtf8($record->recipient_name ?? ''),
                'created_by' => $this->ensureUtf8($record->creator?->name ?? ''),
            ];
        });

        $summaryData = $this->getGroupedByCategory()->map(function ($item) {
            return [
                'category_name' => $item['category_name'],
                'count' => $item['count'],
                'total_amount' => number_format($item['total_amount'], 2),
            ];
        });

        $export = new ExpenseReportExcelExport($detailedData, $summaryData, $this->getExportTitle());
        $filename = $this->getExportFilename('xlsx');

        return Excel::download($export, $filename);
    }

    public function exportToPdfExpense()
    {
        $data = $this->data;
        $tableQuery = $this->getTableQuery();
        $records = $tableQuery->get();

        $detailedRows = $records->map(function ($record) {
            return [
                $record->trx_date?->format('Y-m-d') ?? '',
                $this->ensureUtf8($record->branch?->name ?? ''),
                $this->ensureUtf8($record->country?->name_text ?? ''),
                $this->ensureUtf8($record->currency?->code ?? ''),
                $this->ensureUtf8($record->financeType?->name_text ?? ''),
                number_format((float) $record->amount, 2),
                $this->ensureUtf8($record->payment_method ?? ''),
                $this->ensureUtf8($record->reference_no ?? ''),
                $this->ensureUtf8($record->recipient_name ?? ''),
                $this->ensureUtf8($record->creator?->name ?? ''),
            ];
        })->toArray();

        $summaryRows = $this->getGroupedByCategory()->map(function ($item) {
            return [
                $item['category_name'],
                $item['count'],
                number_format($item['total_amount'], 2),
            ];
        })->toArray();

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

        $summaryHeaders = [
            tr('reports.expense.summary.category', [], null, 'dashboard') ?: 'Category',
            tr('reports.expense.summary.count', [], null, 'dashboard') ?: 'Count',
            tr('reports.expense.summary.total_amount', [], null, 'dashboard') ?: 'Total Amount',
        ];

        $metadata = $this->getExportMetadata();
        $metadata['total_expenses'] = number_format($this->getTotalExpenses(), 2);
        $metadata['transaction_count'] = $this->getTransactionCount();

        $export = new ReportPdfExport(
            collect($detailedRows),
            $headers,
            tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report',
            $metadata,
            app()->getLocale() === 'ar',
            'reports.expense-report-pdf'
        );

        $export->setSummaryData(collect($summaryRows), $summaryHeaders);

        $filename = $this->getExportFilename('pdf');

        return $export->download($filename);
    }

    protected function getExportTitle(): ?string
    {
        $data = $this->data;
        $from = $data['date_from'] ?? '';
        $to = $data['date_to'] ?? '';
        return tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report' . ' (' . $from . ' to ' . $to . ')';
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $title = 'expense_report';
        return strtolower($title) . '_' . date('Y-m-d_His') . '.' . $extension;
    }

    protected function getExportMetadata(): array
    {
        $data = $this->data;
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
            'date_from' => $data['date_from'] ?? '',
            'date_to' => $data['date_to'] ?? '',
        ];

        if (!empty($data['branch_id'])) {
            $branch = Branch::find($data['branch_id']);
            $metadata['branch'] = $this->ensureUtf8($branch?->name ?? '');
        }

        if (!empty($data['currency_id'])) {
            $currency = Currency::find($data['currency_id']);
            $metadata['currency'] = $this->ensureUtf8($currency?->code ?? '');
        }

        return $metadata;
    }

    protected function ensureUtf8($value): string
    {
        if (is_null($value)) {
            return '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return (string) $value;
        }

        if (!is_string($value)) {
            $value = (string) $value;
        }

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $detected = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1256', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                return $converted;
            }
        }

        if (function_exists('iconv')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($cleaned !== false) {
                return $cleaned;
            }
        }

        $cleaned = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        if (mb_check_encoding($cleaned, 'UTF-8')) {
            return $cleaned;
        }

        return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) ?: '';
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
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.reports.expense') || $user?->can('finance_reports.view') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
