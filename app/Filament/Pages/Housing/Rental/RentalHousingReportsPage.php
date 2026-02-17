<?php

namespace App\Filament\Pages\Housing\Rental;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class RentalHousingReportsPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'rental_housing';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.rental_housing.reports';
    protected static string $view = 'filament.pages.housing.reports';

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.rental_housing.reports', [], null, 'dashboard') ?: 'التقارير';
    }

    public function getTitle(): string
    {
        return tr('housing.reports.heading', [], null, 'dashboard') ?: 'تقارير الإيواء';
    }

    public function getHeading(): string
    {
        return tr('housing.reports.heading', [], null, 'dashboard') ?: 'تقارير الإيواء';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.reports.view') ?? false;
    }

    public function getReturnsThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::rental()
            ->where('entry_type', 'return')
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();
    }

    public function getExitsThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::rental()
            ->whereNotNull('exit_date')
            ->whereMonth('exit_date', now()->month)
            ->whereYear('exit_date', now()->year)
            ->count();
    }

    public function getEntriesThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::rental()
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();
    }

    public function getCurrentResidents(): int
    {
        return \App\Models\Housing\AccommodationEntry::rental()
            ->whereNull('exit_date')
            ->count();
    }

    public function viewReport(string $reportKey): void
    {
        \Filament\Notifications\Notification::make()
            ->title(tr('housing.reports.view_report', [], null, 'dashboard') ?: 'عرض التقرير')
            ->info()
            ->send();
    }
}
