<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\ExportsTable;
use App\Models\Sales\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class InvoicesReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use ExportsTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 11;
    protected static string $view = 'filament.pages.reports.invoices-report';

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
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'partial' => 'Partial',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
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

        $query = Invoice::query()
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->with(['customer', 'currency', 'order']);

        if ($status) {
            $query->where('status', $status);
        }

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

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger' => 'overdue',
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

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'paid' ? 'danger' : null),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Invoices')
                    ->query(fn ($query) => $query->where('due_date', '<', now())
                        ->whereNotIn('status', ['paid', 'cancelled'])),
            ])
            ->defaultSort('invoice_date', 'desc')
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
        
        $title = 'Invoices Report';
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

