<?php

namespace App\Filament\Widgets\Rental;

use App\Models\Rental\RentalContractRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RequestStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(tr('rental.requests.stats.all', [], null, 'dashboard') ?: 'All Requests', RentalContractRequest::count())
                ->description(tr('rental.requests.stats.total', [], null, 'dashboard') ?: 'Total requests')
                ->color('gray'),
            Stat::make(tr('rental.requests.status.pending', [], null, 'dashboard') ?: 'Pending', RentalContractRequest::where('status', 'pending')->count())
                ->description(tr('rental.requests.stats.awaiting_review', [], null, 'dashboard') ?: 'Awaiting review')
                ->color('warning'),
            Stat::make(tr('rental.requests.status.under_review', [], null, 'dashboard') ?: 'Under Review', RentalContractRequest::where('status', 'under_review')->count())
                ->description(tr('rental.requests.stats.being_reviewed', [], null, 'dashboard') ?: 'Being reviewed')
                ->color('info'),
            Stat::make(tr('rental.requests.status.approved', [], null, 'dashboard') ?: 'Approved', RentalContractRequest::where('status', 'approved')->count())
                ->description(tr('rental.requests.stats.ready_to_convert', [], null, 'dashboard') ?: 'Ready to convert')
                ->color('success'),
            Stat::make(tr('rental.requests.status.rejected', [], null, 'dashboard') ?: 'Rejected', RentalContractRequest::where('status', 'rejected')->count())
                ->description(tr('rental.requests.stats.rejected_requests', [], null, 'dashboard') ?: 'Rejected requests')
                ->color('danger'),
            Stat::make(tr('rental.requests.status.converted', [], null, 'dashboard') ?: 'Converted', RentalContractRequest::where('status', 'converted')->count())
                ->description(tr('rental.requests.stats.converted_to_contracts', [], null, 'dashboard') ?: 'Converted to contracts')
                ->color('gray'),
        ];
    }
}
