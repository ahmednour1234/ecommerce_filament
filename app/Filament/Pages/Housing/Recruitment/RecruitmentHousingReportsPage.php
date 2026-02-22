<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class RecruitmentHousingReportsPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'recruitment_housing';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_housing.reports';
    protected static string $view = 'filament.pages.housing.reports';

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.recruitment_housing.reports', [], null, 'dashboard') ?: 'التقارير';
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
        return false;
    }

    public function getReturnsThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::recruitment()
            ->where('entry_type', 'return')
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();
    }

    public function getExitsThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::recruitment()
            ->whereNotNull('exit_date')
            ->whereMonth('exit_date', now()->month)
            ->whereYear('exit_date', now()->year)
            ->count();
    }

    public function getEntriesThisMonth(): int
    {
        return \App\Models\Housing\AccommodationEntry::recruitment()
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();
    }

    public function getCurrentResidents(): int
    {
        return \App\Models\Housing\AccommodationEntry::recruitment()
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
