<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RecruitmentContractsSectionHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.recruitment-contracts-section-header-widget';
    protected static ?int $sort = 29;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment_contracts.view_any');
    }
}
