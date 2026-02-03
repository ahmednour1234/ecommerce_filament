<?php

namespace App\Filament\Pages\Finance;

use App\Exports\IncomeReportExcelExport;
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
use Filament\Infolists\Infolist as FilamentInfolist;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class IncomeReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use ExportsTable;
    use FinanceModuleGate;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationGroup = 'finance';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.finance.income-report';
    protected static ?string $navigationTranslationKey = 'sidebar.finance.income_report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfYear()->format('Y-m-d'),
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
                            ->label(tr('reports.income.filters.date_from', [], null, 'dashboard') ?: 'From Date')
                            ->nullable()
                            ->reactive(),

                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('reports.income.filters.date_to', [], null, 'dashboard') ?: 'To Date')
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('reports.income.filters.branch', [], null, 'dashboard') ?: 'Branch')
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
                            ->label(tr('reports.income.filters.country', [], null, 'dashboard') ?: 'Country')
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
                            ->label(tr('reports.income.filters.currency', [], null, 'dashboard') ?: 'Currency')
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
                            ->label(tr('reports.income.filters.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('reports.income.filters.category', [], null, 'dashboard') ?: 'Category')
                            ->options(function () {
                                return FinanceType::where('kind', 'income')
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
                            ->label(tr('reports.income.filters.search', [], null, 'dashboard') ?: 'Search')
                            ->placeholder(tr('reports.income.filters.search_placeholder', [], null, 'dashboard') ?: 'Search by payer, reference, notes...')
                            ->nullable()
                            ->reactive(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist->schema([]);
    }

    protected function baseQuery(): Builder
    {
        return BranchTransaction::query()
            ->whereHas('financeType', fn (Builder $q) => $q->where('kind', 'income'))
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
                    ->label(tr('reports.income.columns.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('reports.income.columns.branch', [], null, 'dashboard') ?: 'Branch')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->branch?->name ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name_text')
                    ->label(tr('reports.income.columns.country', [], null, 'dashboard') ?: 'Country')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->country?->name_text ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('reports.income.columns.currency', [], null, 'dashboard') ?: 'Currency')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->currency?->code ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label(tr('reports.income.columns.category', [], null, 'dashboard') ?: 'Category')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->financeType?->name_text ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('reports.income.columns.amount', [], null, 'dashboard') ?: 'Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(tr('reports.income.columns.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->payment_method ?? ''))
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('reports.income.columns.reference', [], null, 'dashboard') ?: 'Reference')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->reference_no ?? ''))
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('reports.income.columns.payer', [], null, 'dashboard') ?: 'Payer')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->recipient_name ?? ''))
                    ->searchable(),

                Tables\Columns\IconColumn::make('attachment_path')
                    ->label(tr('reports.income.columns.attachment', [], null, 'dashboard') ?: 'Attachment')
                    ->icon(fn($record) => $record->attachment_path ? 'heroicon-o-paper-clip' : null)
                    ->url(fn($record) => $record->attachment_path ? asset('storage/' . $record->attachment_path) : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('reports.income.columns.created_by', [], null, 'dashboard') ?: 'Created By')
                    ->getStateUsing(fn($record) => $this->ensureUtf8($record->creator?->name ?? ''))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('reports.income.filters.branch', [], null, 'dashboard') ?: 'Branch')
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
                    ->label(tr('reports.income.filters.category', [], null, 'dashboard') ?: 'Category')
                    ->options(function () {
                        return FinanceType::where('kind', 'income')
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
                        return $this->exportToExcelIncome();
                    }),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return $this->exportToPdfIncome();
                    }),
            ])
            ->defaultSort('trx_date', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public function getTotalIncome(): float
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
            ->map(function ($item) {
                return [
                    'category_name' => $this->ensureUtf8($item->financeType?->name_text ?? ''),
                    'count' => (int) $item->count,
                    'total_amount' => (float) $item->total_amount,
                ];
            });
    }

    public function exportToExcelIncome()
    {
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
                'payer' => $this->ensureUtf8($record->recipient_name ?? ''),
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

        $export = new IncomeReportExcelExport($detailedData, $summaryData, $this->getExportTitle());
        $filename = $this->getExportFilename('xlsx');

        return Excel::download($export, $filename);
    }

    public function exportToPdfIncome()
    {
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
            tr('reports.income.columns.date', [], null, 'dashboard') ?: 'Date',
            tr('reports.income.columns.branch', [], null, 'dashboard') ?: 'Branch',
            tr('reports.income.columns.country', [], null, 'dashboard') ?: 'Country',
            tr('reports.income.columns.currency', [], null, 'dashboard') ?: 'Currency',
            tr('reports.income.columns.category', [], null, 'dashboard') ?: 'Category',
            tr('reports.income.columns.amount', [], null, 'dashboard') ?: 'Amount',
            tr('reports.income.columns.payment_method', [], null, 'dashboard') ?: 'Payment Method',
            tr('reports.income.columns.reference', [], null, 'dashboard') ?: 'Reference',
            tr('reports.income.columns.payer', [], null, 'dashboard') ?: 'Payer',
            tr('reports.income.columns.created_by', [], null, 'dashboard') ?: 'Created By',
        ];

        $summaryHeaders = [
            tr('reports.income.summary.category', [], null, 'dashboard') ?: 'Category',
            tr('reports.income.summary.count', [], null, 'dashboard') ?: 'Count',
            tr('reports.income.summary.total_amount', [], null, 'dashboard') ?: 'Total Amount',
        ];

        $metadata = $this->getExportMetadata();
        $metadata['total_income'] = number_format($this->getTotalIncome(), 2);
        $metadata['transaction_count'] = $this->getTransactionCount();

        $export = new ReportPdfExport(
            collect($detailedRows),
            $headers,
            tr('pages.finance.income_report.title', [], null, 'dashboard') ?: 'Income Report',
            $metadata,
            app()->getLocale() === 'ar',
            'reports.income-report-pdf'
        );

        $export->setSummaryData(collect($summaryRows), $summaryHeaders);

        $filename = $this->getExportFilename('pdf');

        return $export->download($filename);
    }

    protected function getExportTitle(): ?string
    {
        $d = $this->form->getRawState();
        $base = tr('pages.finance.income_report.title', [], null, 'dashboard') ?: 'Income Report';
        $from = $d['date_from'] ?? null;
        $to = $d['date_to'] ?? null;

        if (!$from && !$to) return $base;

        return $base . ' (' . ($from ?: '-') . ' to ' . ($to ?: '-') . ')';
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $title = 'income_report';
        return strtolower($title) . '_' . date('Y-m-d_His') . '.' . $extension;
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
        return tr('pages.finance.income_report.title', [], null, 'dashboard') ?: 'Income Report';
    }

    public function getHeading(): string
    {
        return tr('pages.finance.income_report.title', [], null, 'dashboard') ?: 'Income Report';
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('super_admin')
            || $user?->can('finance.reports.income')
            || ($user?->can('finance_reports.view') ?? false);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
