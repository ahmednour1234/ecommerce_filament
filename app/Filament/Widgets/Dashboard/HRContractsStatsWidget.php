<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Recruitment\RecruitmentContract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HRContractsStatsWidget extends BaseWidget
{
    protected static ?int $sort = 30;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'إحصائيات العقود حسب القسم';

    public static function canView(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user?->hasRole('super_admin') || $user?->type === 'hr_manager' || $user?->can('recruitment_contracts.view_any');
    }

    protected function getStats(): array
    {
        // عد العقود حسب القسم (مثال: استخدم الحقول المناسبة في جدول العقود)
        $accountsCount = RecruitmentContract::where('department', 'accounts')->count();
        $coordinationCount = RecruitmentContract::where('department', 'coordination')->count();
        $customerServiceCount = RecruitmentContract::where('department', 'customer_service')->count();
        $deliveredCount = RecruitmentContract::where('status', 'delivered')->count();

        return [
            Stat::make('عقود قسم الحسابات', $accountsCount)
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->url(route('filament.admin.resources.recruitment-contracts.index', ['tableFilters' => ['department' => ['value' => 'accounts']]])),
            Stat::make('عقود قسم التنسيق', $coordinationCount)
                ->icon('heroicon-o-users')
                ->color('warning')
                ->url(route('filament.admin.resources.recruitment-contracts.index', ['tableFilters' => ['department' => ['value' => 'coordination']]])),
            Stat::make('عقود خدمة العملاء', $customerServiceCount)
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->url(route('filament.admin.resources.recruitment-contracts.index', ['tableFilters' => ['department' => ['value' => 'customer_service']]])),
            Stat::make('عقود تم التسليم', $deliveredCount)
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->url(route('filament.admin.resources.recruitment-contracts.index', ['tableFilters' => ['status' => ['value' => 'delivered']]])),
        ];
    }
}
