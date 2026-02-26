<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingAssignment;
use App\Models\Housing\HousingRequest;
use App\Models\MainCore\Branch;
use App\Services\Housing\HousingReportsService;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class RecruitmentHousingReportsPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.recruitment_housing.reports';
    protected static string $view = 'filament.pages.housing.reports';

    public $branch_id = null;
    public $from_date = null;
    public $to_date = null;
    public $activeTab = 'assignments';

    public function getTitle(): string
    {
        return tr('housing.reports.title', [], null, 'dashboard') ?: 'تقارير الإيواء';
    }

    public function getHeading(): string
    {
        return tr('housing.reports.heading', [], null, 'dashboard') ?: 'تقارير الإيواء';
    }

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.reports', [], null, 'dashboard') ?: 'التقارير';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.reports.view') ?? false;
    }

    public function mount(): void
    {
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
    }

    public function getStats(): array
    {
        $assignmentsQuery = $this->getAssignmentsTableQuery();
        $requestsQuery = $this->getRequestsTableQuery();

        // Get assignments with statuses
        $assignments = $assignmentsQuery->with('status')->get();
        
        // Get requests
        $requests = $requestsQuery->get();

        // Count workers by status from assignments
        $statusCounts = [];
        $totalAssignments = $assignments->count();
        
        foreach ($assignments as $assignment) {
            if ($assignment->status) {
                $statusKey = $assignment->status->key ?? 'unknown';
                $statusName = app()->getLocale() === 'ar' 
                    ? $assignment->status->name_ar 
                    : $assignment->status->name_en;
                
                if (!isset($statusCounts[$statusKey])) {
                    $statusCounts[$statusKey] = [
                        'count' => 0,
                        'name' => $statusName,
                        'color' => $assignment->status->color ?? 'gray',
                    ];
                }
                $statusCounts[$statusKey]['count']++;
            }
        }

        // Count workers needing transfer kafala from assignments (by status)
        $transferKafalaCount = $assignments->filter(function ($assignment) {
            return $assignment->status && $assignment->status->key === 'transfer_kafala';
        })->count();

        // Count requests by type and status
        $pendingRequests = $requests->where('status', 'pending')->count();
        $completedRequests = $requests->where('status', 'completed')->count();
        $deliveryRequests = $requests->where('request_type', 'delivery')->count();
        $returnRequests = $requests->where('request_type', 'return')->count();
        $totalRequests = $requests->count();

        // Get unique workers count
        $uniqueWorkers = $assignments->pluck('laborer_id')->unique()->count();

        return [
            'total_assignments' => $totalAssignments,
            'total_workers' => $uniqueWorkers,
            'total_requests' => $totalRequests,
            'transfer_kafala' => $transferKafalaCount,
            'pending_requests' => $pendingRequests,
            'completed_requests' => $completedRequests,
            'delivery_requests' => $deliveryRequests,
            'return_requests' => $returnRequests,
            'status_counts' => $statusCounts,
        ];
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'requests') {
            return $this->requestsTable($table);
        }
        
        return $this->assignmentsTable($table);
    }

    protected function assignmentsTable(Table $table): Table
    {
        return $table
            ->query($this->getAssignmentsTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('tables.housing.workers.name', [], null, 'dashboard') ?: 'اسم العامل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.passport_number')
                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'رقم الجواز')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.nationality.name_ar')
                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'جنسية العاملة')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer?->nationality
                        ? (app()->getLocale() === 'ar' ? $record->laborer->nationality->name_ar : $record->laborer->nationality->name_en)
                        : '')
                    ->sortable(),

                Tables\Columns\TextColumn::make('building.name_ar')
                    ->label(tr('housing.building', [], null, 'dashboard') ?: 'المبنى')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name_ar')
                    ->label(tr('housing.unit', [], null, 'dashboard') ?: 'الوحدة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status.name_ar')
                    ->label(tr('housing.status', [], null, 'dashboard') ?: 'الحالة')
                    ->badge()
                    ->color(fn ($record) => $record->status?->color ?? 'gray')
                    ->formatStateUsing(fn ($state, $record) => $record->status
                        ? (app()->getLocale() === 'ar' ? $record->status->name_ar : $record->status->name_en)
                        : '')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('housing.start_date', [], null, 'dashboard') ?: 'تاريخ البدء')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rent_amount')
                    ->label(tr('housing.rent_amount', [], null, 'dashboard') ?: 'مبلغ الإيجار')
                    ->money('SAR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label(tr('filters.housing.status', [], null, 'dashboard') ?: 'الحالة')
                    ->relationship('status', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('building_id')
                    ->label(tr('filters.housing.building', [], null, 'dashboard') ?: 'المبنى')
                    ->relationship('building', 'name_ar')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('start_date', 'desc');
    }

    protected function requestsTable(Table $table): Table
    {
        return $table
            ->query($this->getRequestsTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label(tr('housing.requests.order_no', [], null, 'dashboard') ?: 'رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('tables.housing.requests.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.nationality.name_ar')
                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'جنسية العاملة')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer?->nationality
                        ? (app()->getLocale() === 'ar' ? $record->laborer->nationality->name_ar : $record->laborer->nationality->name_en)
                        : '')
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_type')
                    ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'delivery' => 'success',
                        'return' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.requests.type.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'completed' => 'success',
                        'approved' => 'info',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.requests.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_date')
                    ->label(tr('housing.requests.request_date', [], null, 'dashboard') ?: 'تاريخ الطلب')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('housing.requests.client', [], null, 'dashboard') ?: 'العميل')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('request_type')
                    ->label(tr('filters.housing.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->options([
                        'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                        'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('filters.housing.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'pending' => tr('housing.requests.status.pending', [], null, 'dashboard') ?: 'معلق',
                        'approved' => tr('housing.requests.status.approved', [], null, 'dashboard') ?: 'موافق عليه',
                        'completed' => tr('housing.requests.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                        'rejected' => tr('housing.requests.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
                    ]),
            ])
            ->defaultSort('request_date', 'desc');
    }

    protected function getAssignmentsTableQuery(): Builder
    {
        $query = HousingAssignment::query()
            ->with(['laborer.nationality', 'building', 'unit', 'status'])
            ->whereNull('end_date');

        if ($this->branch_id) {
            $query->whereHas('building', function ($q) {
                $q->where('branch_id', $this->branch_id);
            });
        }

        if ($this->from_date) {
            $query->whereDate('start_date', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('start_date', '<=', $this->to_date);
        }

        return $query;
    }

    protected function getRequestsTableQuery(): Builder
    {
        $query = HousingRequest::query()
            ->with(['laborer.nationality', 'client', 'building'])
            ->where('housing_type', 'recruitment');

        if ($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        }

        if ($this->from_date) {
            $query->whereDate('request_date', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('request_date', '<=', $this->to_date);
        }

        return $query;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    public function applyFilters(): void
    {
        $this->resetTable();
    }

    public function resetFilters(): void
    {
        $this->branch_id = null;
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
        $this->resetTable();
    }
}
