<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Sales\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Concerns\AccountingModuleGate;

class OrdersReportPage extends Page implements HasTable
{
    use InteractsWithTable,AccountingModuleGate;
    use ExportsTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.orders_report';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.reports.orders-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'status' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filters')
                    ->schema([
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

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now();
        $status = $this->data['status'] ?? null;

        $query = Order::query()
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->with(['customer', 'currency', 'items']);

        if ($status) {
            $query->where('status', $status);
        }

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
                        'gray' => 'refunded',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_amount')
                    ->label('Tax')
                    ->money('USD')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->money('USD')
                    ->toggleable(),

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
            ->defaultSort('order_date', 'desc')
            ->paginated([10, 25, 50, 100]);
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
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now();
        $status = $this->data['status'] ?? null;

        $title = 'Orders Report';
        if ($status) {
            $title .= ' - ' . ucfirst($status);
        }

        return $title . ' (' . $dateFrom . ' to ' . $dateTo . ')';
    }

    protected function getExportMetadata(): array
    {
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
        ];
        $metadata['date_from'] = $this->data['date_from'] ?? '';
        $metadata['date_to'] = $this->data['date_to'] ?? '';
        $metadata['status'] = $this->data['status'] ?? 'All';

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

