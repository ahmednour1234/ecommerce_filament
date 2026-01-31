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
            Stat::make('All Requests', RentalContractRequest::count())
                ->description('Total requests')
                ->color('gray'),
            Stat::make('Pending', RentalContractRequest::where('status', 'pending')->count())
                ->description('Awaiting review')
                ->color('warning'),
            Stat::make('Under Review', RentalContractRequest::where('status', 'under_review')->count())
                ->description('Being reviewed')
                ->color('info'),
            Stat::make('Approved', RentalContractRequest::where('status', 'approved')->count())
                ->description('Ready to convert')
                ->color('success'),
            Stat::make('Rejected', RentalContractRequest::where('status', 'rejected')->count())
                ->description('Rejected requests')
                ->color('danger'),
            Stat::make('Converted', RentalContractRequest::where('status', 'converted')->count())
                ->description('Converted to contracts')
                ->color('gray'),
        ];
    }
}
