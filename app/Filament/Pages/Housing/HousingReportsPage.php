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
        return auth()->user()?->can('housing.reports.view') ?? false;
    }

    public function getReturnsThisMonth(): int
    {
        // TODO: Replace with actual model query when models exist
        return 0;
    }

    public function getExitsThisMonth(): int
    {
        // TODO: Replace with actual model query when models exist
        return 0;
    }

    public function getEntriesThisMonth(): int
    {
        // TODO: Replace with actual model query when models exist
        return 0;
    }

    public function getCurrentResidents(): int
    {
        // TODO: Replace with actual model query when models exist
        return 0;
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
