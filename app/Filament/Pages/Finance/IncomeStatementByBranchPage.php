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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeStatementByBranchPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use FinanceModuleGate;
    use TranslatableNavigation;
    use ExportsTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'finance';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.finance.income-statement-by-branch';
    protected static ?string $navigationTranslationKey = 'sidebar.finance.income_statement_by_branch';

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
            'status' => 'approved',
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
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('from')
                            ->label(tr('reports.income_statement.filters.from', [], null, 'dashboard') ?: 'From Date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('to')
                            ->label(tr('reports.income_statement.filters.to', [], null, 'dashboard') ?: 'To Date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('reports.income_statement.filters.currency', [], null, 'dashboard') ?: 'Currency')
                            ->options(Currency::where('is_active', true)->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('kind')
                            ->label(tr('reports.income_statement.filters.kind', [], null, 'dashboard') ?: 'Kind (Optional)')
                            ->options([
                                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('finance_type_id', null);
                                $this->resetTable();
                            }),

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
                            ->visible(fn ($get) => $get('kind') !== null)
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('status')
                            ->label(tr('reports.income_statement.filters.status', [], null, 'dashboard') ?: 'Status (Optional)')
                            ->options([
                                'pending' => tr('forms.status.pending', [], null, 'dashboard') ?: 'Pending',
                                'approved' => tr('forms.status.approved', [], null, 'dashboard') ?: 'Approved',
                                'rejected' => tr('forms.status.rejected', [], null, 'dashboard') ?: 'Rejected',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

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
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('reports.income_statement.filters.country', [], null, 'dashboard') ?: 'Country (Optional)')
                            ->options(\App\Models\MainCore\Country::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),
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
            } else {
                $query->where('status', 'approved');
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
            } else {
                $query->where('status', 'approved');
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

    public function table(Table $table): Table
    {
        $data = $this->data;
        $incomeTypes = $this->getIncomeTypes();
        $expenseTypes = $this->getExpenseTypes();
        $allRows = [];

        foreach ($incomeTypes as $type) {
            $typeName = $this->preserveUtf8($type['name']);
            $section = $this->preserveUtf8(tr('reports.income_statement.income_section', [], null, 'dashboard') ?: 'INCOME');
            $allRows[] = [
                'id' => 'income_' . $type['id'],
                'section' => $section,
                'type' => $typeName,
                'amount' => $type['total'],
                'finance_type_id' => $type['id'],
            ];
        }

        if (!empty($incomeTypes)) {
            $section = $this->preserveUtf8(tr('reports.income_statement.income_section', [], null, 'dashboard') ?: 'INCOME');
            $totalIncome = $this->preserveUtf8(tr('reports.income_statement.total_income', [], null, 'dashboard') ?: 'Total Income');
            $allRows[] = [
                'id' => 'income_total',
                'section' => $section,
                'type' => $totalIncome,
                'amount' => $this->getTotalIncome(),
                'finance_type_id' => null,
            ];
        }

        foreach ($expenseTypes as $type) {
            $typeName = $this->preserveUtf8($type['name']);
            $section = $this->preserveUtf8(tr('reports.income_statement.expense_section', [], null, 'dashboard') ?: 'EXPENSE');
            $allRows[] = [
                'id' => 'expense_' . $type['id'],
                'section' => $section,
                'type' => $typeName,
                'amount' => $type['total'],
                'finance_type_id' => $type['id'],
            ];
        }

        if (!empty($expenseTypes)) {
            $section = $this->preserveUtf8(tr('reports.income_statement.expense_section', [], null, 'dashboard') ?: 'EXPENSE');
            $totalExpense = $this->preserveUtf8(tr('reports.income_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense');
            $allRows[] = [
                'id' => 'expense_total',
                'section' => $section,
                'type' => $totalExpense,
                'amount' => $this->getTotalExpense(),
                'finance_type_id' => null,
            ];
        }

        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        $unionQueries = [];
        foreach ($allRows as $row) {
            $id = (string)$row['id'];
            $section = mb_convert_encoding($row['section'] ?? '', 'UTF-8', 'UTF-8');
            $type = mb_convert_encoding($row['type'] ?? '', 'UTF-8', 'UTF-8');
            $amount = (float)$row['amount'];
            $financeTypeId = $row['finance_type_id'];
            
            if (!mb_check_encoding($section, 'UTF-8')) {
                $section = mb_convert_encoding($section, 'UTF-8', mb_detect_encoding($section, ['UTF-8', 'Windows-1256', 'ISO-8859-6'], true) ?: 'UTF-8');
            }
            if (!mb_check_encoding($type, 'UTF-8')) {
                $type = mb_convert_encoding($type, 'UTF-8', mb_detect_encoding($type, ['UTF-8', 'Windows-1256', 'ISO-8859-6'], true) ?: 'UTF-8');
            }
            
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as section, ? as type, ? as amount, ? as finance_type_id', [
                $id,
                $section,
                $type,
                $amount,
                $financeTypeId,
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw("'empty' as id, NULL as section, NULL as type, 0 as amount, NULL as finance_type_id");
        }

        return $table
            ->query(fn () => BranchTransaction::query()
                ->fromSub($unionQuery, 'income_statement_data')
                ->select('income_statement_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->label(tr('reports.income_statement.section', [], null, 'dashboard') ?: 'Section')
                    ->badge()
                    ->color(fn ($record) => str_contains($record->section ?? '', 'INCOME') ? 'success' : 'danger')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(tr('reports.income_statement.type', [], null, 'dashboard') ?: 'Type')
                    ->searchable()
                    ->weight(fn ($record) => str_contains($record->type ?? '', 'Total') ? 'bold' : 'normal')
                    ->url(function ($record) use ($data) {
                        if (empty($record->finance_type_id) || str_contains($record->type ?? '', 'Total')) {
                            return null;
                        }

                        $url = \App\Filament\Resources\Finance\BranchTransactionResource::getUrl('index');
                        $params = [];

                        if (!empty($record->finance_type_id)) {
                            $params['tableFilters[finance_type_id][value]'] = $record->finance_type_id;
                        }

                        if (!empty($data['branch_id'])) {
                            $params['tableFilters[branch_id][value]'] = $data['branch_id'];
                        }

                        if (!empty($data['currency_id'])) {
                            $params['tableFilters[currency_id][value]'] = $data['currency_id'];
                        }

                        if (!empty($data['from'])) {
                            $params['tableFilters[trx_date][from]'] = $data['from'];
                        }

                        if (!empty($data['to'])) {
                            $params['tableFilters[trx_date][to]'] = $data['to'];
                        }

                        $status = $data['status'] ?? 'approved';
                        $params['tableFilters[status][value]'] = $status;

                        $queryString = http_build_query($params);
                        $fullUrl = $url . ($queryString ? '?' . $queryString : '');

                        if (str_starts_with($fullUrl, 'http://') || str_starts_with($fullUrl, 'https://')) {
                            $parsed = parse_url($fullUrl);
                            $scheme = $parsed['scheme'] ?? 'https';
                            $host = $parsed['host'] ?? '';
                            $path = $parsed['path'] ?? '';
                            $query = $parsed['query'] ?? '';
                            return $scheme . '://' . $host . '/public' . $path . ($query ? '?' . $query : '');
                        }

                        return '/public' . $fullUrl;
                    })
                    ->openUrlInNewTab(false)
                    ->color('primary')
                    ->icon(fn ($record) => !empty($record->finance_type_id) && !str_contains($record->type ?? '', 'Total') ? 'heroicon-o-arrow-top-right-on-square' : null)
                    ->tooltip(fn ($record) => !empty($record->finance_type_id) && !str_contains($record->type ?? '', 'Total') ? tr('reports.income_statement.view_transactions', [], null, 'dashboard') ?: 'View Transactions' : null),
                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('reports.income_statement.total', [], null, 'dashboard') ?: 'Total')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(fn () => ' ' . (Currency::find($data['currency_id'] ?? null)?->code ?? ''))
                    ->color(fn ($record) => str_contains($record->section ?? '', 'INCOME') ? 'success' : 'danger')
                    ->weight(fn ($record) => str_contains($record->type ?? '', 'Total') ? 'bold' : 'normal')
                    ->alignEnd(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $table = $this->table($this->makeTable());
                        return $this->exportToExcel($table, $this->getExportFilename('xlsx'));
                    }),

                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->requiresConfirmation(false)
                    ->action(function () {
                        return $this->downloadPdf();
                    }),
            ])
            ->defaultSort('section')
            ->paginated(false);
    }

    public function getTableRecordKey($record): string
    {
        return (string) ($record->id ?? uniqid());
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

    protected function getExportTitle(): ?string
    {
        $title = tr('reports.income_statement.title', [], null, 'dashboard') ?: 'Income Statement by Branch';
        return $this->sanitizeUtf8($title);
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $data = $this->data;
        $branch = Branch::find($data['branch_id'] ?? null);
        $currency = Currency::find($data['currency_id'] ?? null);

        $parts = ['income_statement'];
        if ($branch) {
            $parts[] = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $branch->name));
        }
        if ($currency) {
            $parts[] = $currency->code;
        }
        $parts[] = date('Y-m-d_His');

        return implode('_', $parts) . '.' . $extension;
    }

    protected function getExportMetadata(): array
    {
        $data = $this->data;
        $branch = Branch::find($data['branch_id'] ?? null);
        $currency = Currency::find($data['currency_id'] ?? null);
        $financeType = !empty($data['finance_type_id']) ? FinanceType::find($data['finance_type_id']) : null;

        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => $this->sanitizeUtf8(auth()->user()?->name ?? 'System'),
            'branch' => $this->sanitizeUtf8($branch?->name ?? 'N/A'),
            'currency' => $this->sanitizeUtf8($currency?->code ?? 'N/A'),
            'from_date' => $data['from'] ?? 'N/A',
            'to_date' => $data['to'] ?? 'N/A',
        ];

        if ($financeType) {
            $metadata['finance_type'] = $this->sanitizeUtf8($financeType->name_text);
        }

        if (!empty($data['status'])) {
            $metadata['status'] = $this->sanitizeUtf8($data['status']);
        }

        if (!empty($data['payment_method'])) {
            $metadata['payment_method'] = $this->sanitizeUtf8($data['payment_method']);
        }

        if (!empty($data['kind'])) {
            $metadata['kind'] = $this->sanitizeUtf8($data['kind']);
        }

        return array_map(function($value) {
            if (is_string($value)) {
                return $this->sanitizeUtf8($value);
            }
            if (is_array($value)) {
                return array_map([$this, 'sanitizeUtf8'], $value);
            }
            return $value;
        }, $metadata);
    }

    public function downloadPdf()
    {
        try {
            $table = $this->table($this->makeTable());
            $exportData = $this->getTableDataForExport($table);
            $title = $this->preserveUtf8($this->getExportTitle() ?? 'Report');
            $filename = $this->preserveUtf8($this->getExportFilename('pdf'));
            $metadata = $this->getExportMetadata();

            $sanitizedDataArray = [];
            foreach ($exportData['data'] as $row) {
                $sanitizedRow = [];
                foreach ($row as $key => $value) {
                    $cleanKey = $this->preserveUtf8($key);
                    $cleanValue = $this->preserveUtf8($value);
                    $sanitizedRow[$cleanKey] = $cleanValue;
                }
                $sanitizedDataArray[] = $sanitizedRow;
            }

            $sanitizedHeaders = [];
            foreach ($exportData['headers'] as $header) {
                $sanitizedHeaders[] = $this->preserveUtf8($header);
            }

            $sanitizedMetadata = [];
            foreach ($metadata as $key => $value) {
                $sanitizedKey = $this->preserveUtf8($key);
                if (is_string($value)) {
                    $sanitizedMetadata[$sanitizedKey] = $this->preserveUtf8($value);
                } elseif (is_array($value)) {
                    $sanitizedMetadata[$sanitizedKey] = array_map([$this, 'preserveUtf8'], $value);
                } else {
                    $sanitizedMetadata[$sanitizedKey] = is_numeric($value) ? $value : $this->preserveUtf8((string)$value);
                }
            }

            $test1 = json_encode($sanitizedDataArray, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            $test2 = json_encode($sanitizedHeaders, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            $test3 = json_encode($sanitizedMetadata, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);

            if ($test1 === false || $test2 === false || $test3 === false) {
                throw new \RuntimeException('Invalid UTF-8 data detected after sanitization');
            }

            session()->flash('income_statement_pdf_export', [
                'data' => $sanitizedDataArray,
                'headers' => $sanitizedHeaders,
                'title' => $title,
                'filename' => $filename,
                'metadata' => $sanitizedMetadata,
            ]);

            return redirect()->route('filament.exports.income-statement-pdf');
        } catch (\Exception $e) {
            $safeMessage = $this->sanitizeUtf8($e->getMessage());
            $safeTrace = $this->sanitizeUtf8($e->getTraceAsString());
            Log::error('PDF Export Error: ' . $safeMessage . PHP_EOL . $safeTrace);

            $exception = new \RuntimeException('PDF export failed: ' . $safeMessage, $e->getCode(), $e);
            throw $exception;
        }
    }

    protected function getTableDataForExport(\Filament\Tables\Table $table): array
    {
        $tableQuery = $table->getQuery();
        $columns = $this->extractTableColumns($table);

        $records = $tableQuery->get();

        $formattedData = [];
        foreach ($records as $record) {
            $row = [];
            foreach ($columns as $column) {
                $key = $column['name'];
                $label = $this->preserveUtf8($column['label']);
                $value = $this->getColumnValue($record, $key, $column);

                $cleanValue = $this->preserveUtf8($value);
                $row[$label] = $cleanValue;
            }
            $formattedData[] = $row;
        }

        $headers = [];
        foreach (array_column($columns, 'label') as $header) {
            $cleanHeader = $this->preserveUtf8($header);
            $headers[] = $cleanHeader;
        }

        $result = [
            'data' => collect($formattedData),
            'headers' => $headers,
        ];

        $finalTest = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($finalTest === false) {
            throw new \RuntimeException('Export data contains invalid UTF-8 after sanitization');
        }

        return $result;
    }

    protected function getColumnValue($record, string $key, array $column): mixed
    {
        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            $value = $record;
            foreach ($parts as $part) {
                if (is_object($value) && isset($value->$part)) {
                    $value = $value->$part;
                } elseif (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return '';
                }
            }
            return $value ?? '';
        }

        if (is_object($record)) {
            $value = $record->$key ?? $record->getAttribute($key) ?? '';

            if (is_numeric($value) && (str_contains($key, 'total') || str_contains($key, 'amount') || str_contains($key, 'price'))) {
                return number_format((float) $value, 2);
            }

            if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                return $value->format('Y-m-d');
            }

            return $value;
        }

        return $record[$key] ?? '';
    }

    protected function preserveUtf8($value): string
    {
        if (is_null($value)) return '';
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_numeric($value)) return (string) $value;

        if (!is_string($value)) {
            $value = (string) $value;
        }

        if (empty($value)) return '';

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $detected = @mb_detect_encoding($value, ['UTF-8', 'Windows-1256', 'ISO-8859-6', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = @mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                return $converted;
            }
        }

        if (function_exists('iconv')) {
            $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($iconv !== false) {
                return $iconv;
            }
        }

        return $value;
    }

    protected function sanitizeUtf8($value): string
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

        if (empty($value)) {
            return '';
        }

        // Remove invalid UTF-8 bytes first
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $value);

        // Check if valid UTF-8 and can be JSON encoded
        if (mb_check_encoding($value, 'UTF-8')) {
            $test = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        $detected = @mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1256', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = @mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                $test = json_encode($converted, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
                if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                    return $converted;
                }
            }
        }

        if (function_exists('iconv')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $value);
            if ($cleaned !== false && mb_check_encoding($cleaned, 'UTF-8')) {
                $test = json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
                if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                    return $cleaned;
                }
            }
        }

        $cleaned = $this->removeInvalidUtf8($value);
        $converted = @mb_convert_encoding($cleaned, 'UTF-8', 'UTF-8');
        if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
            $test = json_encode($converted, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                return $converted;
            }
        }

        // Final fallback: filter through json_encode/decode
        $json = @json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
        if ($json !== false) {
            $decoded = json_decode($json, true);
            if ($decoded !== null && is_string($decoded)) {
                return $decoded;
            }
        }

        return preg_replace('/[^\x20-\x7E\x{00A0}-\x{FFFF}]/u', '', $value) ?: '';
    }

    protected function removeInvalidUtf8(string $value): string
    {
        if (function_exists('mb_convert_encoding')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        if (function_exists('iconv')) {
            $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($value === false) {
                $value = '';
            }
        }

        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = utf8_encode($value);
        }

        return $value ?: '';
    }
}
