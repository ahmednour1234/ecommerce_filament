<?php

namespace App\Filament\Pages\Rental;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Pages\Page;

class RentalReportsPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Rental';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'navigation.rental_reports';

    protected static string $view = 'filament.pages.rental.reports';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.reports.view') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
