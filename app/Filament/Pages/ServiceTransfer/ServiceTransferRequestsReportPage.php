<?php

namespace App\Filament\Pages\ServiceTransfer;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\TranslatableNavigation;
use Modules\ServiceTransfer\Entities\ServiceTransfer;
use App\Models\MainCore\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class ServiceTransferRequestsReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use ExportsTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'نقل الخدمات';
    protected static ?string $navigationLabel = 'تقرير طلبات نقل الخدمات';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.service-transfer.requests-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'branch_id' => null,
            'payment_status' => null,
            'request_status' => null,
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

                        Forms\Components\Select::make('branch_id')
                            ->label('الفرع')
                            ->options(function () {
                                return Branch::active()->get()->pluck('name', 'id')->toArray();
                            })
                            ->nullable()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'unpaid' => 'غير مدفوع',
                                'partial' => 'جزئي',
                                'paid' => 'مدفوع',
                                'refunded' => 'مسترد',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('request_status')
                            ->label('حالة الطلب')
                            ->options([
                                'active' => 'نشط',
                                'archived' => 'مؤرشف',
                                'refunded' => 'مسترد',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),
                    ])
                    ->columns(5),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now();
        $branchId = $this->data['branch_id'] ?? null;
        $paymentStatus = $this->data['payment_status'] ?? null;
        $requestStatus = $this->data['request_status'] ?? null;

        $query = ServiceTransfer::query()
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->with(['branch', 'customer', 'worker', 'package', 'nationality']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($requestStatus) {
            $query->where('request_status', $requestStatus);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('request_no')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_date')
                    ->label('تاريخ الطلب')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('worker.name_ar')
                    ->label('العاملة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('request_status')
                    ->label('حالة الطلب')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'archived',
                        'danger' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'نشط',
                        'archived' => 'مؤرشف',
                        'refunded' => 'مسترد',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'paid' => 'مدفوع',
                        'unpaid' => 'غير مدفوع',
                        'partial' => 'جزئي',
                        'refunded' => 'مسترد',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ الإجمالي')
                    ->money('SAR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('SAR'),
                    ]),
            ])
            ->defaultSort('request_date', 'desc')
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
        $branchId = $this->data['branch_id'] ?? null;
        $branch = $branchId ? Branch::find($branchId) : null;

        $title = 'تقرير طلبات نقل الخدمات';
        if ($branch) {
            $title .= ' - ' . $branch->name;
        }

        return $title . ' (' . $dateFrom . ' إلى ' . $dateTo . ')';
    }

    protected function getExportMetadata(): array
    {
        $branchId = $this->data['branch_id'] ?? null;
        $branch = $branchId ? Branch::find($branchId) : null;

        return [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
            'date_from' => $this->data['date_from'] ?? '',
            'date_to' => $this->data['date_to'] ?? '',
            'branch' => $branch ? $branch->name : 'الكل',
            'payment_status' => $this->data['payment_status'] ?? 'الكل',
            'request_status' => $this->data['request_status'] ?? 'الكل',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->can('service_transfer.reports.view') ?? false;
    }
}
