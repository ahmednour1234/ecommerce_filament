<?php

namespace App\Filament\Pages\Housing\Rental;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Laborer;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RentalHousingAvailableWorkersPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'إيواء التأجير';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.rental_housing.available_workers';
    protected static string $view = 'filament.pages.housing.available-workers';

    public function getTitle(): string
    {
        return tr('housing.available_workers.title', [], null, 'dashboard') ?: 'العمالة المتاحة';
    }

    public function getHeading(): string
    {
        return tr('housing.available_workers.heading', [], null, 'dashboard') ?: 'العمالة المتاحة';
    }

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.available_workers', [], null, 'dashboard') ?: 'العمالة المتاحة';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.available_workers.view_any') ?? false;
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

                Tables\Columns\IconColumn::make('is_available')
                    ->label(tr('tables.housing.workers.available', [], null, 'dashboard') ?: 'متاح')
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return Laborer::query()
            ->where('is_available', true)
            ->whereDoesntHave('housingAssignments', function ($query) {
                $query->whereNull('end_date');
            })
            ->with(['nationality', 'profession']);
    }
}
