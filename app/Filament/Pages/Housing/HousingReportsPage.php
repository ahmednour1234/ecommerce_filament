<?php

namespace App\Filament\Pages\Housing;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class HousingReportsPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'housing';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.reports';
    protected static string $view = 'filament.pages.housing.reports';

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.reports', [], null, 'dashboard') ?: 'التقارير';
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
        return false; // Hidden - replaced by RecruitmentHousingReportsPage and RentalHousingReportsPage
    }

    public function getReturnsThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::where('entry_type', 'return')
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();
    }

    public function getExitsThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::whereNotNull('exit_date')
            ->whereMonth('exit_date', now()->month)
            ->whereYear('exit_date', now()->year)
            ->count();
    }

    public function getEntriesThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();
    }

    public function getCurrentResidents(): int
    {
        return \App\Models\Housing\AccommodationEntry::whereNull('exit_date')->count();
    }

    public function viewReport(string $reportKey): void
    {
        // TODO: Implement report viewing logic
        \Filament\Notifications\Notification::make()
            ->title(tr('housing.reports.view_report', [], null, 'dashboard') ?: 'عرض التقرير')
            ->info()
            ->send();
    }
}
