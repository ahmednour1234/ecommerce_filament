<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class HRSectionHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr-section-header-widget';
    protected static ?int $sort = 30;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('super_admin') || $user?->type === 'hr_manager' || $user?->can('hr.leave_requests.view_any') || $user?->can('hr_excuse_requests.view_any');
    }
}
