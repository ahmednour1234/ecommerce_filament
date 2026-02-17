<?php

namespace App\Filament\Pages\ServiceTransfer;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\ServiceTransferPayment;
use App\Models\MainCore\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class ServiceTransferPaymentsReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use ExportsTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'نقل الخدمات';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationTranslationKey = 'sidebar.servicetransferpaymentsreport';
    protected static string $view = 'filament.pages.service-transfer.payments-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'payment_method_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('الفلاتر')
                    ->schema([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('من تاريخ')
                            ->required()
                            ->default(now()->startOfMonth())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\DatePicker::make('date_to')
                            ->label('إلى تاريخ')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('payment_method_id')
                            ->label('طريقة الدفع')
                            ->options(function () {
                                return PaymentMethod::where('is_active', true)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->nullable()
                            ->searchable()
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
        $paymentMethodId = $this->data['payment_method_id'] ?? null;

        $query = ServiceTransferPayment::query()
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->with(['transfer.customer', 'transfer.worker', 'paymentMethod', 'createdBy']);

        if ($paymentMethodId) {
            $query->where('payment_method_id', $paymentMethodId);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('payment_no')
                    ->label('رقم الدفعة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer.request_no')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer.customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('SAR'),
                    ]),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('طريقة الدفع')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('أنشئ بواسطة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_excel')
                ->label('تصدير إلى Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return $this->exportToExcel(null, $this->getExportFilename('xlsx'));
                }),

            \Filament\Actions\Action::make('export_pdf')
                ->label('تصدير إلى PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    return $this->exportToPdf(null, $this->getExportFilename('pdf'));
                }),

            \Filament\Actions\Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportTitle(): ?string
    {
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now();
        $paymentMethodId = $this->data['payment_method_id'] ?? null;
        $paymentMethod = $paymentMethodId ? PaymentMethod::find($paymentMethodId) : null;

        $title = 'تقرير المدفوعات - نقل الخدمات';
        if ($paymentMethod) {
            $title .= ' - ' . $paymentMethod->name;
        }

        return $title . ' (' . $dateFrom . ' إلى ' . $dateTo . ')';
    }

    protected function getExportMetadata(): array
    {
        $paymentMethodId = $this->data['payment_method_id'] ?? null;
        $paymentMethod = $paymentMethodId ? PaymentMethod::find($paymentMethodId) : null;

        return [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
            'date_from' => $this->data['date_from'] ?? '',
            'date_to' => $this->data['date_to'] ?? '',
            'payment_method' => $paymentMethod ? $paymentMethod->name : 'الكل',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->can('service_transfer.reports.view') ?? false;
    }
}
