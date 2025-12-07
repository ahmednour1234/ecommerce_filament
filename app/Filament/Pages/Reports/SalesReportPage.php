<?php

namespace App\Filament\Pages\Reports;

use App\Models\Sales\Invoice;
use App\Models\Sales\Order;
use App\Models\Sales\Customer;
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

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 2;
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

        // Wrap Query Builder in Eloquent Builder using Invoice model
        $query = Invoice::query()
            ->fromSub($unionQuery, 'revenue_data')
            ->select('revenue_data.*');

        return $table
            ->query($query)
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

        // Wrap Query Builder in Eloquent Builder using Customer model
        $query = Customer::query()
            ->fromSub($unionQuery, 'customer_report_data')
            ->select('customer_report_data.*');

        return $table
            ->query($query)
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

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Export logic would go here
                }),
        ];
    }
}

