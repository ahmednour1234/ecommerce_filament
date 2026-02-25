<?php

namespace App\Filament\Pages\Housing;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingRequest;
use App\Services\Housing\HousingDashboardStatsService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class HousingDashboardPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'قسم الإيواء';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.dashboard';
    protected static string $view = 'filament.pages.housing.dashboard';

    public ?string $request_type = null;
    public ?string $status = null;
    public ?string $from_date = null;
    public ?string $to_date = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.dashboard', [], null, 'dashboard') ?: 'لوحة التحكم';
    }

    public function getTitle(): string
    {
        return tr('housing.dashboard.heading', [], null, 'dashboard') ?: 'لوحة تحكم الإيواء';
    }

    public function getHeading(): string
    {
        return tr('housing.dashboard.heading', [], null, 'dashboard') ?: 'لوحة تحكم الإيواء';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.dashboard.view') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return ['form'];
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('request_type')
                    ->label(tr('housing.dashboard.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->options([
                        'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                        'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                    ])
                    ->columnSpan(1),

                \Filament\Forms\Components\Select::make('status')
                    ->label(tr('housing.dashboard.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'pending' => tr('housing.leave.status.pending', [], null, 'dashboard') ?: 'معلقة',
                        'approved' => tr('housing.dashboard.approved_requests', [], null, 'dashboard') ?: 'موافق عليها',
                        'completed' => tr('housing.dashboard.completed_requests', [], null, 'dashboard') ?: 'مكتملة',
                    ])
                    ->columnSpan(1),

                \Filament\Forms\Components\DatePicker::make('from_date')
                    ->label(tr('housing.dashboard.from_date', [], null, 'dashboard') ?: 'من تاريخ')
                    ->columnSpan(1),

                \Filament\Forms\Components\DatePicker::make('to_date')
                    ->label(tr('housing.dashboard.to_date', [], null, 'dashboard') ?: 'إلى تاريخ')
                    ->columnSpan(1),
            ])
            ->columns(4)
            ->statePath('data');
    }

    public function getStats(): array
    {
        $service = new HousingDashboardStatsService();
        return $service->getRequestStats([
            'request_type' => $this->request_type,
            'status' => $this->status,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);
    }

    public function getCompletedCount(): int
    {
        return $this->getStats()['completed'] ?? 0;
    }

    public function getApprovedCount(): int
    {
        return $this->getStats()['approved'] ?? 0;
    }

    public function getPendingCount(): int
    {
        return $this->getStats()['pending'] ?? 0;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label(tr('tables.housing.requests.order_no', [], null, 'dashboard') ?: 'رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('tables.housing.requests.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_type')
                    ->label(tr('tables.housing.requests.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->badge()
                    ->formatStateUsing(fn ($state) => tr("housing.requests.type.{$state}", [], null, 'dashboard') ?: $state),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'completed' => 'success',
                        'approved' => 'info',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('request_date')
                    ->label(tr('tables.housing.requests.request_date', [], null, 'dashboard') ?: 'تاريخ الطلب')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('request_type')
                    ->label(tr('filters.housing.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->options([
                        'new_rent' => tr('housing.requests.type.new_rent', [], null, 'dashboard') ?: 'إيجار جديد',
                        'cancel_rent' => tr('housing.requests.type.cancel_rent', [], null, 'dashboard') ?: 'إلغاء الإيجار',
                        'transfer_kafala' => tr('housing.requests.type.transfer_kafala', [], null, 'dashboard') ?: 'نقل الكفالة',
                        'outside_service' => tr('housing.requests.type.outside_service', [], null, 'dashboard') ?: 'خارج الخدمة',
                        'leave_request' => tr('housing.requests.type.leave_request', [], null, 'dashboard') ?: 'طلب إجازة',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('filters.housing.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'pending' => tr('housing.requests.status.pending', [], null, 'dashboard') ?: 'معلق',
                        'approved' => tr('housing.requests.status.approved', [], null, 'dashboard') ?: 'موافق عليه',
                        'completed' => tr('housing.requests.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                        'rejected' => tr('housing.requests.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
                        'suspended' => tr('housing.requests.status.suspended', [], null, 'dashboard') ?: 'موقوف',
                    ]),
            ])
            ->defaultSort('request_date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $query = HousingRequest::query()->with(['laborer']);

        if ($this->request_type) {
            $query->where('request_type', $this->request_type);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->from_date) {
            $query->whereDate('request_date', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('request_date', '<=', $this->to_date);
        }

        return $query;
    }

    public function search(): void
    {
        // TODO: Implement search logic
        $this->dispatch('$refresh');
    }

    public function resetFilters(): void
    {
        $this->request_type = null;
        $this->status = null;
        $this->from_date = null;
        $this->to_date = null;
        $this->form->fill();
    }
}
