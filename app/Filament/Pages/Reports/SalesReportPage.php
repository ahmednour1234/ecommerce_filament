<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\ExportsTable;
use App\Models\Sales\Invoice;
use App\Models\Sales\Order;
use App\Models\Sales\Customer;
use App\Models\MainCore\Branch;
use App\Models\Accounting\Account;
use App\Services\Accounting\ReportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;

class SalesReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use ExportsTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 9;
    protected static string $view = 'filament.pages.reports.sales-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'report_type' => 'orders',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Filters')
                    ->schema([
                        Forms\Components\Select::make('report_type')
                            ->label('Report Type')
                            ->options([
                                'orders' => 'Orders Report',
                                'invoices' => 'Invoices Report',
                                'revenue' => 'Revenue Report',
                                'customers' => 'Customers Report',
                                'income_statement' => 'Income Statement',
                            ])
                            ->required()
                            ->default('orders')
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date')
                            ->required()
                            ->default(now()->startOfMonth())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('date_to')
                            ->label('To Date')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(Branch::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable())
                            ->visible(fn ($get) => $get('report_type') === 'income_statement'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $reportType = $this->data['report_type'] ?? 'orders';
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now();

        if ($reportType === 'orders') {
            return $this->ordersTable($table, $dateFrom, $dateTo);
        } elseif ($reportType === 'invoices') {
            return $this->invoicesTable($table, $dateFrom, $dateTo);
        } elseif ($reportType === 'revenue') {
            return $this->revenueTable($table, $dateFrom, $dateTo);
        } elseif ($reportType === 'income_statement') {
            return $this->incomeStatementTable($table, $dateFrom, $dateTo);
        } else {
            return $this->customersTable($table, $dateFrom, $dateTo);
        }
    }

    protected function ordersTable(Table $table, $dateFrom, $dateTo): Table
    {
        $query = Order::query()
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->with(['customer', 'currency']);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('USD'),
                    ]),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),
            ])
            ->defaultSort('order_date', 'desc');
    }

    protected function invoicesTable(Table $table, $dateFrom, $dateTo): Table
    {
        $query = Invoice::query()
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->with(['customer', 'currency']);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger' => 'overdue',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('USD'),
                    ]),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('invoice_date', 'desc');
    }

    protected function revenueTable(Table $table, $dateFrom, $dateTo): Table
    {
        // Build Query Builder for aggregated revenue data
        $unionQuery = DB::table('invoices')
            ->select(
                DB::raw('DATE(invoice_date) as date'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(CASE WHEN status = "paid" OR paid_at IS NOT NULL THEN total ELSE 0 END) as paid_revenue')
            )
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date', 'desc');

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => Invoice::query()
                ->fromSub($unionQuery, 'revenue_data')
                ->select('revenue_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_count')
                    ->label('Invoices')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Revenue')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_revenue')
                    ->label('Paid Revenue')
                    ->money('USD')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function customersTable(Table $table, $dateFrom, $dateTo): Table
    {
        // Build Query Builder for aggregated customer data
        $unionQuery = DB::table('customers')
            ->leftJoin('orders', function ($join) use ($dateFrom, $dateTo) {
                $join->on('customers.id', '=', 'orders.customer_id')
                    ->whereBetween('orders.order_date', [$dateFrom, $dateTo]);
            })
            ->leftJoin('invoices', function ($join) use ($dateFrom, $dateTo) {
                $join->on('customers.id', '=', 'invoices.customer_id')
                    ->whereBetween('invoices.invoice_date', [$dateFrom, $dateTo]);
            })
            ->select(
                'customers.id',
                'customers.name',
                'customers.code',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('COUNT(DISTINCT invoices.id) as invoice_count'),
                DB::raw('COALESCE(SUM(orders.total), 0) as total_orders'),
                DB::raw('COALESCE(SUM(invoices.total), 0) as total_invoices')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.code')
            ->havingRaw('order_count > 0 OR invoice_count > 0');

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => Customer::query()
                ->fromSub($unionQuery, 'customer_report_data')
                ->select('customer_report_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_count')
                    ->label('Orders')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_count')
                    ->label('Invoices')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Orders Total')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_invoices')
                    ->label('Invoices Total')
                    ->money('USD')
                    ->sortable(),
            ])
            ->defaultSort('total_invoices', 'desc');
    }

    protected function incomeStatementTable(Table $table, $dateFrom, $dateTo): Table
    {
        $reportService = app(ReportService::class);
        $branchId = $this->data['branch_id'] ?? null;
        
        $filters = [
            'from_date' => $dateFrom,
            'to_date' => $dateTo,
        ];
        
        if ($branchId) {
            $filters['branch_id'] = $branchId;
        }
        
        $incomeStatement = $reportService->getIncomeStatement($filters);
        
        // Combine revenue and expense details into a single dataset
        $data = [];
        
        // Add revenue section
        $data[] = (object) [
            'type' => 'header',
            'account_code' => '',
            'account_name' => 'REVENUE',
            'amount' => null,
        ];
        
        foreach ($incomeStatement['revenue_details'] as $item) {
            $data[] = (object) [
                'type' => 'revenue',
                'account_code' => $item['account_code'],
                'account_name' => $item['account_name'],
                'amount' => $item['amount'],
            ];
        }
        
        $data[] = (object) [
            'type' => 'total',
            'account_code' => '',
            'account_name' => 'Total Revenue',
            'amount' => $incomeStatement['revenue'],
        ];
        
        // Add expense section
        $data[] = (object) [
            'type' => 'header',
            'account_code' => '',
            'account_name' => 'EXPENSES',
            'amount' => null,
        ];
        
        foreach ($incomeStatement['expense_details'] as $item) {
            $data[] = (object) [
                'type' => 'expense',
                'account_code' => $item['account_code'],
                'account_name' => $item['account_name'],
                'amount' => $item['amount'],
            ];
        }
        
        $data[] = (object) [
            'type' => 'total',
            'account_code' => '',
            'account_name' => 'Total Expenses',
            'amount' => $incomeStatement['expenses'],
        ];
        
        // Add net income
        $data[] = (object) [
            'type' => 'net',
            'account_code' => '',
            'account_name' => 'NET INCOME',
            'amount' => $incomeStatement['net_income'],
        ];
        
        // Build union query from data
        $unionQueries = [];
        foreach ($data as $item) {
            $unionQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0')
                ->selectRaw('? as type, ? as account_code, ? as account_name, ? as amount', [
                    $item->type,
                    $item->account_code ?? '',
                    $item->account_name ?? '',
                    $item->amount ?? 0,
                ]);
        }
        
        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            if ($unionQuery === null) {
                $unionQuery = $uq;
            } else {
                $unionQuery->union($uq);
            }
        }
        
        if ($unionQuery === null) {
            $unionQuery = DB::table('accounts')->whereRaw('1 = 0')
                ->selectRaw('NULL as type, NULL as account_code, NULL as account_name, 0 as amount');
        }
        
        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => Account::query()
                ->fromSub($unionQuery, 'income_statement_data')
                ->select('income_statement_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('account_code')
                    ->label('Account Code')
                    ->searchable(false)
                    ->sortable(false)
                    ->formatStateUsing(fn ($record) => $record->account_code ?: ''),

                Tables\Columns\TextColumn::make('account_name')
                    ->label('Account Name')
                    ->searchable(false)
                    ->sortable(false)
                    ->formatStateUsing(function ($record) {
                        $name = $record->account_name;
                        if ($record->type === 'header') {
                            return '<strong>' . $name . '</strong>';
                        }
                        if ($record->type === 'total' || $record->type === 'net') {
                            return '<strong>' . $name . '</strong>';
                        }
                        return $name;
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(false)
                    ->formatStateUsing(function ($record) {
                        if ($record->type === 'header') {
                            return '';
                        }
                        return $record->amount ?? 0;
                    })
                    ->color(function ($record) {
                        if ($record->type === 'net') {
                            return $record->amount >= 0 ? 'success' : 'danger';
                        }
                        return null;
                    }),
            ])
            ->defaultSort('account_code', 'asc')
            ->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_excel')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return $this->exportToExcel(null, $this->getExportFilename('xlsx'));
                }),

            \Filament\Actions\Action::make('export_pdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    return $this->exportToPdf(null, $this->getExportFilename('pdf'));
                }),

            \Filament\Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportTitle(): ?string
    {
        $reportType = $this->data['report_type'] ?? 'orders';
        $titles = [
            'orders' => 'Orders Report',
            'invoices' => 'Invoices Report',
            'revenue' => 'Revenue Report',
            'customers' => 'Customers Report',
            'income_statement' => 'Income Statement',
        ];
        
        $title = $titles[$reportType] ?? 'Report';
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now();
        
        return $title . ' (' . $dateFrom . ' to ' . $dateTo . ')';
    }

    protected function getExportMetadata(): array
    {
        $metadata = parent::getExportMetadata();
        $metadata['report_type'] = $this->data['report_type'] ?? 'orders';
        $metadata['date_from'] = $this->data['date_from'] ?? '';
        $metadata['date_to'] = $this->data['date_to'] ?? '';
        
        if (isset($this->data['branch_id'])) {
            $branch = Branch::find($this->data['branch_id']);
            $metadata['branch'] = $branch?->name ?? '';
        }
        
        return $metadata;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Check for any report permission or default to true
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Check if user has any report permission
        $hasReportPermission = $user->can('reports.trial_balance') ||
                              $user->can('reports.general_ledger') ||
                              $user->can('reports.account_statement') ||
                              $user->can('reports.income_statement') ||
                              $user->can('reports.balance_sheet') ||
                              $user->can('reports.cash_flow');
        
        return $hasReportPermission;
    }
}

