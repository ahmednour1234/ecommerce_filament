<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class ComplaintsStatsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = null;

    protected $listeners = ['filters-updated' => '$refresh'];

    public function getHeading(): string
    {
        return 'إحصائيات الشكاوي';
    }

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
        $from = $filters['date_from'] ?? now()->startOfMonth();
        $to = $filters['date_to'] ?? now()->endOfMonth();
        $branchId = $filters['branch_id'] ?? null;

        if (is_string($from)) {
            $from = Carbon::parse($from)->startOfDay();
        }
        if (is_string($to)) {
            $to = Carbon::parse($to)->endOfDay();
        }

        $cacheKey = "dashboard_complaints_stats_{$branchId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $baseFilters = [
                'created_at' => [
                    'created_from' => $from->format('Y-m-d'),
                    'created_until' => $to->format('Y-m-d'),
                ],
            ];
            
            if ($branchId) {
                $baseFilters['branch_id'] = ['value' => $branchId];
            }

            $baseUrl = ComplaintResource::getUrl('index');
            // Extract path from URL if it's a full URL
            $publicUrl = $this->normalizeUrl($baseUrl);
            $query = Complaint::query()
                ->whereBetween('created_at', [$from, $to]);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalComplaints = $query->count();
            
            // Priority-based counts
            $urgentComplaints = (clone $query)->where('priority', 'urgent')->count();
            $highComplaints = (clone $query)->where('priority', 'high')->count();
            $mediumComplaints = (clone $query)->where('priority', 'medium')->count();
            $lowComplaints = (clone $query)->where('priority', 'low')->count();
            
            // Status-based counts
            $pendingComplaints = (clone $query)->where('status', 'pending')->count();
            $inProgressComplaints = (clone $query)->where('status', 'in_progress')->count();
            $resolvedComplaints = (clone $query)->where('status', 'resolved')->count();
            $closedComplaints = (clone $query)->where('status', 'closed')->count();

            $stats = [];

            if ($totalComplaints === 0) {
                return [
                    Stat::make(
                        'لا توجد بيانات',
                        'لا توجد شكاوي في الفترة المحددة'
                    )
                        ->description('')
                        ->color('gray')
                        ->icon('heroicon-o-information-circle'),
                ];
            }

            // Total Complaints - Main Overview
            $stats[] = Stat::make(
                'إجمالي الشكاوي',
                Number::format($totalComplaints)
            )
                ->description('في الفترة المحددة')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document-list')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            // Priority Section - Organized by Priority Level (Most Important First)
            if ($urgentComplaints > 0) {
                $urgentPending = (clone $query)->where('priority', 'urgent')->where('status', 'pending')->count();
                $urgentInProgress = (clone $query)->where('priority', 'urgent')->where('status', 'in_progress')->count();
                $urgentDescription = ($urgentPending > 0 || $urgentInProgress > 0)
                    ? 'يحتاج إلى انتباه فوري'
                    : 'عاجل';
                
                $stats[] = Stat::make(
                    'شكاوي عاجلة',
                    Number::format($urgentComplaints)
                )
                    ->description($urgentDescription)
                    ->descriptionIcon('heroicon-m-fire')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'priority' => ['value' => 'urgent'],
                    ])));
            }

            if ($highComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي عالية الأولوية',
                    Number::format($highComplaints)
                )
                    ->description('عالي')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'priority' => ['value' => 'high'],
                    ])));
            }

            if ($mediumComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي متوسطة الأولوية',
                    Number::format($mediumComplaints)
                )
                    ->description('متوسط')
                    ->descriptionIcon('heroicon-m-minus-circle')
                    ->color('info')
                    ->icon('heroicon-o-minus-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'priority' => ['value' => 'medium'],
                    ])));
            }

            if ($lowComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي منخفضة الأولوية',
                    Number::format($lowComplaints)
                )
                    ->description('منخفض')
                    ->descriptionIcon('heroicon-m-arrow-down')
                    ->color('gray')
                    ->icon('heroicon-o-arrow-down')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'priority' => ['value' => 'low'],
                    ])));
            }

            // Status Section - Organized by Status
            if ($pendingComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي قيد الانتظار',
                    Number::format($pendingComplaints)
                )
                    ->description('قيد الانتظار')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('warning')
                    ->icon('heroicon-o-clock')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'pending'],
                    ])));
            }

            if ($inProgressComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي قيد المعالجة',
                    Number::format($inProgressComplaints)
                )
                    ->description('قيد المعالجة')
                    ->descriptionIcon('heroicon-m-arrow-path')
                    ->color('info')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'in_progress'],
                    ])));
            }

            if ($resolvedComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي تم حلها',
                    Number::format($resolvedComplaints)
                )
                    ->description('تم الحل')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'resolved'],
                    ])));
            }

            if ($closedComplaints > 0) {
                $stats[] = Stat::make(
                    'شكاوي مغلقة',
                    Number::format($closedComplaints)
                )
                    ->description('مغلق')
                    ->descriptionIcon('heroicon-m-check-badge')
                    ->color('gray')
                    ->icon('heroicon-o-check-badge')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'closed'],
                    ])));
            }

            return $stats;
        });
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
