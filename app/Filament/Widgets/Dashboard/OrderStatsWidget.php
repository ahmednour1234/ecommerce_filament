<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Sales\OrderResource;
use App\Services\Dashboard\DashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class OrderStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'إحصائيات الطلبات';

    protected $listeners = ['filters-updated' => '$refresh'];

    protected function getFilters(): array
    {
        if (session()->has('dashboard_filters')) {
            return session()->get('dashboard_filters');
        }
        return \App\Helpers\DashboardFilterHelper::parseFiltersFromRequest();
    }

    protected function getStats(): array
    {
        $filters = $this->getFilters();
        $service = app(DashboardService::class);
        $stats = $service->getOrderStats($filters);

        $from = $filters['date_from'] ?? now()->startOfMonth();
        $to = $filters['date_to'] ?? now()->endOfMonth();

        if (is_string($from)) {
            $from = Carbon::parse($from)->startOfDay();
        }
        if (is_string($to)) {
            $to = Carbon::parse($to)->endOfDay();
        }

        $baseFilters = [
            'order_date' => [
                'created_from' => $from->format('Y-m-d'),
                'created_until' => $to->format('Y-m-d'),
            ],
        ];

        $baseUrl = OrderResource::getUrl('index');
        // Extract path from URL if it's a full URL
        $publicUrl = $this->normalizeUrl($baseUrl);

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
                ->icon('heroicon-o-clock')
                ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                    'status' => ['value' => 'pending'],
                ])));
        }

        if ($stats['processing'] > 0) {
            $statsArray[] = Stat::make('قيد المعالجة', Number::format($stats['processing']))
                ->description('طلبات قيد المعالجة')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color('info')
                ->icon('heroicon-o-cog-6-tooth')
                ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                    'status' => ['value' => 'processing'],
                ])));
        }

        if ($stats['completed'] > 0) {
            $statsArray[] = Stat::make('مكتمل', Number::format($stats['completed']))
                ->description('طلبات مكتملة')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                    'status' => ['value' => 'completed'],
                ])));
        }

        if ($stats['cancelled'] > 0) {
            $statsArray[] = Stat::make('ملغي', Number::format($stats['cancelled']))
                ->description('طلبات ملغاة')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                    'status' => ['value' => 'cancelled'],
                ])));
        }

        if ($stats['refunded'] > 0) {
            $statsArray[] = Stat::make('مسترد', Number::format($stats['refunded']))
                ->description('طلبات مستردة')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                    'status' => ['value' => 'refunded'],
                ])));
        }

        $statsArray[] = Stat::make('إجمالي الطلبات', Number::format($stats['total']))
            ->description('جميع الطلبات في الفترة المحددة')
            ->descriptionIcon('heroicon-o-shopping-bag')
            ->color('primary')
            ->icon('heroicon-o-shopping-bag')
            ->url($this->buildUrl($publicUrl, $baseFilters));

        return $statsArray;
    }

    protected function normalizeUrl(string $url): string
    {
        // If it's already a full URL, extract just the path
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '/';
            // Remove /public if it's already in the path
            if (str_starts_with($path, '/public')) {
                return $path;
            }
            return '/public' . $path;
        }
        
        // If it's already a relative path starting with /public, return as is
        if (str_starts_with($url, '/public')) {
            return $url;
        }
        
        // Otherwise, prepend /public
        return '/public' . $url;
    }

    protected function buildUrl(string $baseUrl, array $filters): string
    {
        $params = [];
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $params["tableFilters[{$key}][{$subKey}]"] = $subValue;
                }
            } else {
                $params["tableFilters[{$key}][value]"] = $value;
            }
        }
        
        $queryString = http_build_query($params);
        return $baseUrl . ($queryString ? '?' . $queryString : '');
    }
}
