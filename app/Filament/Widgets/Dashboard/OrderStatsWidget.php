<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Pages\Dashboard;
use App\Services\Dashboard\DashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'إحصائيات الطلبات';

    protected function getStats(): array
    {
        $filters = \App\Helpers\DashboardFilterHelper::parseFiltersFromRequest();
        $service = app(DashboardService::class);
        $stats = $service->getOrderStats($filters);

        if ($stats['total'] === 0) {
            return [
                Stat::make('لا توجد بيانات', 'لا توجد طلبات في الفترة المحددة')
                    ->description('')
                    ->color('gray')
                    ->icon('heroicon-o-information-circle'),
            ];
        }

        $statsArray = [];

        if ($stats['pending'] > 0) {
            $statsArray[] = Stat::make('قيد الانتظار', Number::format($stats['pending']))
                ->description('طلبات قيد الانتظار')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->icon('heroicon-o-clock');
        }

        if ($stats['processing'] > 0) {
            $statsArray[] = Stat::make('قيد المعالجة', Number::format($stats['processing']))
                ->description('طلبات قيد المعالجة')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color('info')
                ->icon('heroicon-o-cog-6-tooth');
        }

        if ($stats['completed'] > 0) {
            $statsArray[] = Stat::make('مكتمل', Number::format($stats['completed']))
                ->description('طلبات مكتملة')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->icon('heroicon-o-check-circle');
        }

        if ($stats['cancelled'] > 0) {
            $statsArray[] = Stat::make('ملغي', Number::format($stats['cancelled']))
                ->description('طلبات ملغاة')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->icon('heroicon-o-x-circle');
        }

        if ($stats['refunded'] > 0) {
            $statsArray[] = Stat::make('مسترد', Number::format($stats['refunded']))
                ->description('طلبات مستردة')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left');
        }

        $statsArray[] = Stat::make('إجمالي الطلبات', Number::format($stats['total']))
            ->description('جميع الطلبات في الفترة المحددة')
            ->descriptionIcon('heroicon-o-shopping-bag')
            ->color('primary')
            ->icon('heroicon-o-shopping-bag');

        return $statsArray;
    }
}
