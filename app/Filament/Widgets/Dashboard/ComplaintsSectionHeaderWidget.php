<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ComplaintsSectionHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.complaints-section-header-widget';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('super_admin') || $user?->can('complaints.view_any') || false;
    }
}
