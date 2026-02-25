<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\Building;
use App\Models\Housing\HousingAssignment;
use App\Models\Housing\HousingStatus;
use App\Models\Recruitment\Laborer;
use App\Services\Housing\HousingDashboardStatsService;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecruitmentHousingWorkersPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.recruitment_housing.workers';
    protected static string $view = 'filament.pages.housing.workers';

    public function getTitle(): string
    {
        return tr('housing.workers.title', [], null, 'dashboard') ?: 'العمالة';
    }

    public function getHeading(): string
    {
        return tr('housing.workers.heading', [], null, 'dashboard') ?: 'العمالة';
    }

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.workers', [], null, 'dashboard') ?: 'العمالة';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(tr('tables.housing.workers.id', [], null, 'dashboard') ?: 'رقم')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('personal_image')
                    ->label(tr('tables.housing.workers.image', [], null, 'dashboard') ?: 'الصورة')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('tables.housing.workers.name', [], null, 'dashboard') ?: 'الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nationality.name_ar')
                    ->label(tr('tables.housing.workers.nationality', [], null, 'dashboard') ?: 'الدولة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('passport_number')
                    ->label(tr('tables.housing.workers.passport', [], null, 'dashboard') ?: 'رقم الجواز')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profession.name_ar')
                    ->label(tr('tables.housing.workers.profession', [], null, 'dashboard') ?: 'المهنة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('activeHousingAssignment.status.name_ar')
                    ->label(tr('tables.housing.workers.status', [], null, 'dashboard') ?: 'الحالة')
                    ->badge()
                    ->color(fn ($record) => $record->activeHousingAssignment?->status?->color ?? 'gray'),

                Tables\Columns\TextColumn::make('activeHousingAssignment.building.name_ar')
                    ->label(tr('tables.housing.workers.branch', [], null, 'dashboard') ?: 'الفرع')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label(tr('tables.housing.workers.rating', [], null, 'dashboard') ?: 'التقييم')
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('building_id')
                    ->label(tr('filters.housing.building', [], null, 'dashboard') ?: 'المبنى')
                    ->options(Building::pluck('name_ar', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('housingAssignments', function ($q) use ($data) {
                            $q->where('building_id', $data['value'])->whereNull('end_date');
                        });
                    }),

                Tables\Filters\SelectFilter::make('status_id')
                    ->label(tr('filters.housing.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options(HousingStatus::pluck('name_ar', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('housingAssignments', function ($q) use ($data) {
                            $q->where('status_id', $data['value'])->whereNull('end_date');
                        });
                    }),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label(tr('filters.housing.available', [], null, 'dashboard') ?: 'متاح')
                    ->placeholder('الكل')
                    ->trueLabel('متاح فقط')
                    ->falseLabel('غير متاح'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label(tr('actions.edit', [], null, 'dashboard') ?: 'تعديل')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Laborer $record): string => \App\Filament\Resources\Recruitment\LaborerResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('id', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return Laborer::query()
            ->whereHas('housingAssignments', function ($q) {
                $q->whereNull('end_date');
            })
            ->with(['nationality', 'profession', 'activeHousingAssignment.status', 'activeHousingAssignment.building']);
    }

    public function getStats(): array
    {
        $service = new HousingDashboardStatsService();
        return $service->getWorkerCountsByStatus();
    }
}
