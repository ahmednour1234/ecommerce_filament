<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class FinanceSectionHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.finance-section-header-widget';

    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin')
            || $user->type === User::TYPE_ACCOUNTANT
            || $user->type === User::TYPE_GENERAL_ACCOUNTANT
            || $user->can('finance.view_reports')
            || $user->can('finance.view_transactions');
    }
}
