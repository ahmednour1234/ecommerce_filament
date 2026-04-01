<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RentalSectionHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.rental-section-header-widget';
    protected static ?int $sort = 40;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.view_any') || $user?->can('rental.requests.view_any') ?? false;
    }
}
