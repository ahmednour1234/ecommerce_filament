<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingAssignment;
use App\Models\Housing\AccommodationEntry;
use App\Models\Housing\HousingStatus;
use App\Models\Complaint;
use App\Models\MainCore\Branch;
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

        // Get assignments with statuses
        $assignments = $assignmentsQuery->with('status')->get();

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

        // Get unique workers count
        $uniqueWorkers = $assignments->pluck('laborer_id')->unique()->count();

        // Get accommodation entries query
        $accommodationQuery = AccommodationEntry::query()
            ->where('type', 'recruitment')
            ->whereNull('exit_date');

        if ($this->branch_id) {
            $accommodationQuery->where('branch_id', $this->branch_id);
        }

        if ($this->from_date) {
            $accommodationQuery->whereDate('entry_date', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $accommodationQuery->whereDate('entry_date', '<=', $this->to_date);
        }

        // Total workers in accommodation
        $totalWorkersInAccommodation = $accommodationQuery->distinct('laborer_id')->count('laborer_id');

        // Ready for travel count
        $readyForTravelStatus = HousingStatus::where('key', 'ready_for_travel')->first();
        $readyForTravelCount = $accommodationQuery->clone()
            ->where('status_id', $readyForTravelStatus?->id)
            ->count();

        // New arrivals count
        $newArrivalsCount = $accommodationQuery->clone()
            ->where('entry_type', 'new_arrival')
            ->count();

        // Total complaints
        $complaintsQuery = Complaint::query();
        if ($this->branch_id) {
            $complaintsQuery->where('branch_id', $this->branch_id);
        }
        if ($this->from_date) {
            $complaintsQuery->whereDate('created_at', '>=', $this->from_date);
        }
        if ($this->to_date) {
            $complaintsQuery->whereDate('created_at', '<=', $this->to_date);
        }
        $totalComplaints = $complaintsQuery->count();

        return [
            'total_assignments' => $totalAssignments,
            'total_workers' => $uniqueWorkers,
            'total_workers_in_accommodation' => $totalWorkersInAccommodation,
            'total_complaints' => $totalComplaints,
            'ready_for_travel' => $readyForTravelCount,
            'new_arrivals' => $newArrivalsCount,
            'transfer_kafala' => $transferKafalaCount,
            'status_counts' => $statusCounts,
        ];
    }

    public function table(Table $table): Table
    {
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
