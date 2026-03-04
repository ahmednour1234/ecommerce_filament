<?php

namespace App\Filament\Pages\Housing\Rental;

use App\Data\SaudiGovernorates;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\AccommodationEntry;
use App\Models\Housing\HousingStatus;
use App\Models\Recruitment\Nationality;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class RentalHousingDashboardPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'إيواء التأجير';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.rental_housing.dashboard';
    protected static string $view = 'filament.pages.housing.dashboard';

    public ?int $status_id = null;
    public ?int $branch_id = null;
    public ?int $nationality_id = null;
    public ?string $city = null;
    public ?string $from_date = null;
    public ?string $to_date = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.rental_housing.dashboard', [], null, 'dashboard') ?: 'لوحة تحكم قسم الإيواء';
    }

    public function getTitle(): string
    {
        return tr('housing.dashboard.heading', [], null, 'dashboard') ?: 'لوحة تحكم قسم الإيواء';
    }

    public function getHeading(): string
    {
        return tr('housing.dashboard.heading', [], null, 'dashboard') ?: 'لوحة تحكم قسم الإيواء';
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
                \Filament\Forms\Components\Select::make('status_id')
                    ->label(tr('housing.dashboard.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options(function () {
                        return HousingStatus::active()
                            ->ordered()
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->columnSpan(1),

                \Filament\Forms\Components\Select::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name', fn ($query) => $query->where('is_active', true))
                    ->searchable()
                    ->columnSpan(1),

                \Filament\Forms\Components\Select::make('nationality_id')
                    ->label('الجنسية')
                    ->options(function () {
                        return Nationality::where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($nationality) {
                                $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                return [$nationality->id => $label];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->columnSpan(1),

                \Filament\Forms\Components\Select::make('city')
                    ->label('المدينة')
                    ->options(SaudiGovernorates::all())
                    ->searchable()
                    ->columnSpan(1),

                \Filament\Forms\Components\DatePicker::make('from_date')
                    ->label(tr('housing.dashboard.from_date', [], null, 'dashboard') ?: 'من تاريخ')
                    ->columnSpan(1),

                \Filament\Forms\Components\DatePicker::make('to_date')
                    ->label(tr('housing.dashboard.to_date', [], null, 'dashboard') ?: 'إلى تاريخ')
                    ->columnSpan(1),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function getStats(): array
    {
        $query = AccommodationEntry::rental()
            ->whereNull('exit_date');
        
        if ($this->status_id) $query->where('status_id', $this->status_id);
        if ($this->branch_id) $query->where('branch_id', $this->branch_id);
        if ($this->nationality_id) $query->where('nationality_id', $this->nationality_id);
        if ($this->from_date) $query->whereDate('entry_date', '>=', $this->from_date);
        if ($this->to_date) $query->whereDate('entry_date', '<=', $this->to_date);
        
        $results = $query->select('status_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('status_id')
            ->get();
        
        $stats = [];
        $total = 0;
        foreach ($results as $result) {
            $statusId = $result->status_id;
            $count = (int) $result->count;
            $stats[$statusId] = $count;
            $total += $count;
        }
        
        return ['by_status' => $stats, 'total' => $total];
    }

    public function getCompletedCount(): int
    {
        $query = AccommodationEntry::rental()
            ->whereNull('exit_date');
        
        if ($this->status_id) $query->where('status_id', $this->status_id);
        if ($this->branch_id) $query->where('branch_id', $this->branch_id);
        if ($this->nationality_id) $query->where('nationality_id', $this->nationality_id);
        if ($this->from_date) $query->whereDate('entry_date', '>=', $this->from_date);
        if ($this->to_date) $query->whereDate('entry_date', '<=', $this->to_date);
        
        return $query->count();
    }

    public function getApprovedCount(): int
    {
        return $this->getCompletedCount();
    }

    public function getPendingCount(): int
    {
        return $this->getCompletedCount();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label('اسم العامل')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer 
                        ? (app()->getLocale() === 'ar' ? $record->laborer->name_ar : $record->laborer->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_no')
                    ->label('رقم العقد')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status.name')
                    ->label('الحالة')
                    ->formatStateUsing(fn ($state, $record) => $record->status 
                        ? (app()->getLocale() === 'ar' ? $record->status->name_ar : $record->status->name_en)
                        : '')
                    ->badge()
                    ->color(fn ($record) => $record->status?->color ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nationality.name_ar')
                    ->label('الجنسية')
                    ->formatStateUsing(fn ($state, $record) => $record->nationality 
                        ? (app()->getLocale() === 'ar' ? $record->nationality->name_ar : $record->nationality->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('building.branch.name')
                    ->label('المدينة')
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('entry_date')
                    ->label('تاريخ الدخول')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('الحالة')
                    ->relationship('status', 'name_ar')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label('الجنسية')
                    ->relationship('nationality', 'name_ar')
                    ->searchable(),
            ])
            ->defaultSort('entry_date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $query = AccommodationEntry::rental()
            ->whereNull('exit_date')
            ->with(['laborer', 'status', 'branch', 'nationality']);

        if ($this->status_id) {
            $query->where('status_id', $this->status_id);
        }

        if ($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        }

        if ($this->nationality_id) {
            $query->where('nationality_id', $this->nationality_id);
        }

        if ($this->city) {
            $query->whereHas('building.branch', function ($q) {
                $q->where('name', 'like', '%' . $this->city . '%');
            });
        }

        if ($this->from_date) {
            $query->whereDate('entry_date', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('entry_date', '<=', $this->to_date);
        }

        return $query;
    }

    public function search(): void
    {
        $this->resetTable();
    }

    public function resetFilters(): void
    {
        $this->status_id = null;
        $this->branch_id = null;
        $this->nationality_id = null;
        $this->city = null;
        $this->from_date = null;
        $this->to_date = null;
        $this->form->fill();
        $this->resetTable();
    }
}
