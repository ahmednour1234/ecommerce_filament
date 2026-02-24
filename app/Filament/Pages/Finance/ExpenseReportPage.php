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
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseReportPage extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use ExportsTable;
    use FinanceModuleGate;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationGroup = 'finance';
    protected static ?string $navigationLabel = 'تقرير المصروفات';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.finance.expense-report';
    protected static ?string $navigationTranslationKey = 'sidebar.finance.expense_report';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->baseQuery())
            ->columns([
                Tables\Columns\TextColumn::make('trx_date')
                    ->label(tr('reports.expense.columns.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('reports.expense.columns.branch', [], null, 'dashboard') ?: 'Branch')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->branch?->name ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(tr('reports.expense.columns.country', [], null, 'dashboard') ?: 'Country')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->country?->name ?? ''))
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
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->payment_method ?? '')),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('reports.expense.columns.reference', [], null, 'dashboard') ?: 'Reference')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->reference_no ?? '')),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('reports.expense.columns.receiver', [], null, 'dashboard') ?: 'Receiver')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->recipient_name ?? '')),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('reports.expense.columns.created_by', [], null, 'dashboard') ?: 'Created By')
                    ->getStateUsing(fn ($record) => $this->ensureUtf8($record->creator?->name ?? ''))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('trx_date_range')
                    ->label(tr('reports.expense.filters.date_range', [], null, 'dashboard') ?: 'Date Range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('trx_date', '>=', $date))
                            ->when($data['to'] ?? null, fn (Builder $q, $date) => $q->whereDate('trx_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('reports.expense.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(fn () => Branch::where('status', 'active')
                        ->pluck('name', 'id')
                        ->map(fn ($v) => $this->ensureUtf8($v))
                        ->toArray())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label(tr('reports.expense.filters.country', [], null, 'dashboard') ?: 'Country')
                    ->options(fn () => Country::where('is_active', true)
                        ->pluck('name', 'id')
                        ->map(fn ($v) => $this->ensureUtf8($v))
                        ->toArray())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label(tr('reports.expense.filters.currency', [], null, 'dashboard') ?: 'Currency')
                    ->options(fn () => Currency::where('is_active', true)
                        ->pluck('code', 'id')
                        ->map(fn ($v) => $this->ensureUtf8($v))
                        ->toArray())
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
                        \Filament\Forms\Components\TextInput::make('value'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            fn (Builder $q, $v) => $q->where('payment_method', 'like', '%' . $v . '%')
                        );
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->searchable()
            ->searchPlaceholder(tr('reports.expense.filters.search_placeholder', [], null, 'dashboard') ?: 'Search by receiver, reference, notes...')
            ->modifyQueryUsing(function (Builder $query) {
                $search = $this->getTableSearch();
                if (!$search) return;

                $query->where(function (Builder $q) use ($search) {
                    $q->where('recipient_name', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('notes', 'like', '%' . $search . '%')
                        ->orWhere('payment_method', 'like', '%' . $search . '%');
                });
            })
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

    protected function baseQuery(): Builder
    {
        return BranchTransaction::query()
            ->whereHas('financeType', fn (Builder $q) => $q->where('kind', 'expense'))
            ->with(['financeType', 'branch', 'country', 'currency', 'creator']);
    }

    protected function exportQuery(): Builder
    {
        return $this->getFilteredTableQuery()
            ->clone()
            ->with(['financeType', 'branch', 'country', 'currency', 'creator']);
    }

    public function exportToExcelExpense()
    {
        $records = $this->exportQuery()->get();

        $detailedData = $records->map(fn ($r) => [
            'date' => $r->trx_date?->format('Y-m-d') ?? '',
            'branch' => $this->ensureUtf8($r->branch?->name ?? ''),
            'country' => $this->ensureUtf8($r->country?->name?? ''),
            'currency' => $this->ensureUtf8($r->currency?->code ?? ''),
            'category' => $this->ensureUtf8($r->financeType?->name_text ?? ''),
            'amount' => number_format((float) $r->amount, 2),
            'payment_method' => $this->ensureUtf8($r->payment_method ?? ''),
            'reference_no' => $this->ensureUtf8($r->reference_no ?? ''),
            'receiver' => $this->ensureUtf8($r->recipient_name ?? ''),
            'created_by' => $this->ensureUtf8($r->creator?->name ?? ''),
        ]);

        $summaryData = $this->groupedByCategoryFromQuery($this->exportQuery());

        $export = new ExpenseReportExcelExport($detailedData, $summaryData, $this->getExportTitleFromTable());
        return Excel::download($export, $this->getExportFilename('xlsx'));
    }

    public function exportToPdfExpense()
    {
        $records = $this->exportQuery()->get();

        $detailedRows = $records->map(fn ($r) => [
            $r->trx_date?->format('Y-m-d') ?? '',
            $this->ensureUtf8($r->branch?->name ?? ''),
            $this->ensureUtf8($r->country?->name ?? ''),
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

        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => Auth::user()?->name ?? 'System',
        ];

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
        $query = $this->getFilteredTableQuery();
        return (float) ($query->sum('amount') ?? 0);
    }

    public function getTransactionCount(): int
    {
        $query = $this->getFilteredTableQuery();
        return (int) $query->count();
    }

    public function getGroupedByCategory(): Collection
    {
        $query = $this->getFilteredTableQuery();

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

    protected function groupedByCategoryFromQuery(Builder $query)
    {
        $q = $query->clone();

        return $q
            ->select('finance_type_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('finance_type_id')
            ->with('financeType:id,name')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($item) => [
                'category_name' => $this->ensureUtf8($item->financeType?->name_text ?? ''),
                'count' => (int) $item->count,
                'total_amount' => number_format((float) $item->total_amount, 2),
            ]);
    }

    protected function getExportTitleFromTable(): string
    {
        $base = tr('pages.finance.expense_report.title', [], null, 'dashboard') ?: 'Expense Report';

        $filters = $this->getTableFiltersForm()?->getState() ?? [];

        $from = $filters['trx_date_range']['from'] ?? null;
        $to = $filters['trx_date_range']['to'] ?? null;

        if (!$from && !$to) return $base;

        return $base . ' (' . ($from ?: '-') . ' to ' . ($to ?: '-') . ')';
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        return 'expense_report_' . now()->format('Y-m-d_His') . '.' . $extension;
    }

    protected function ensureUtf8($value): string
    {
        if (is_null($value)) return '';
        if (is_array($value)) return '';
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
