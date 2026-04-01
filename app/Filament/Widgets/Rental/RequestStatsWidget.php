<?php

namespace App\Filament\Widgets\Rental;

use App\Filament\Resources\Rental\RentalRequestsResource;
use App\Models\Rental\RentalContractRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RequestStatsWidget extends BaseWidget
{
    protected static ?int $sort = 42;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.requests.view_any') ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(tr('rental.requests.stats.all', [], null, 'dashboard') ?: 'All Requests', RentalContractRequest::count())
                ->description(tr('rental.requests.stats.total', [], null, 'dashboard') ?: 'Total requests')
                ->color('gray')
                ->url(RentalRequestsResource::getUrl('index')),
            Stat::make(tr('rental.requests.status.pending', [], null, 'dashboard') ?: 'Pending', RentalContractRequest::where('status', 'pending')->count())
                ->description(tr('rental.requests.stats.awaiting_review', [], null, 'dashboard') ?: 'Awaiting review')
                ->color('warning')
                ->url(RentalRequestsResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'pending'],
                    ],
                ])),
            Stat::make(tr('rental.requests.stats.awaiting_approval', [], null, 'dashboard') ?: 'عقود قيد الموافقة', RentalContractRequest::where('status', 'under_review')->count())
                ->description(tr('rental.requests.stats.awaiting_approval_desc', [], null, 'dashboard') ?: 'عقود قيد الموافقة')
                ->color('info')
                ->url(RentalRequestsResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'under_review'],
                    ],
                ])),
            Stat::make(tr('rental.requests.stats.approved', [], null, 'dashboard') ?: 'موافق عليه', RentalContractRequest::where('status', 'approved')->count())
                ->description(tr('rental.requests.stats.approved_desc', [], null, 'dashboard') ?: 'موافق عليه')
                ->color('success')
                ->url(RentalRequestsResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'approved'],
                    ],
                ])),
            Stat::make(tr('rental.requests.stats.rejected', [], null, 'dashboard') ?: 'مرفوض', RentalContractRequest::where('status', 'rejected')->count())
                ->description(tr('rental.requests.stats.rejected_desc', [], null, 'dashboard') ?: 'مرفوض')
                ->color('danger')
                ->url(RentalRequestsResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'rejected'],
                    ],
                ])),
            Stat::make(tr('rental.requests.stats.active_contracts', [], null, 'dashboard') ?: 'عقود التأجير النشطة', RentalContractRequest::where('status', 'converted')->count())
                ->description(tr('rental.requests.stats.active_contracts_desc', [], null, 'dashboard') ?: 'عقود التأجير النشطة')
                ->color('gray')
                ->url(RentalRequestsResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'converted'],
                    ],
                ])),
        ];
    }
}
