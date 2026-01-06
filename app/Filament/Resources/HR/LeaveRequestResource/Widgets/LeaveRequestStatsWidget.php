<?php

namespace App\Filament\Resources\HR\LeaveRequestResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeaveRequestStatsWidget extends BaseWidget
{
    public ?array $data = null;

    protected function getStats(): array
    {
        $data = $this->data ?? [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        return [
            Stat::make(tr('stats.total_requests', [], null, 'dashboard') ?: 'Total Requests', $data['total'])
                ->icon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make(tr('stats.pending_requests', [], null, 'dashboard') ?: 'Pending', $data['pending'])
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make(tr('stats.approved_requests', [], null, 'dashboard') ?: 'Approved', $data['approved'])
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make(tr('stats.rejected_requests', [], null, 'dashboard') ?: 'Rejected', $data['rejected'])
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}

