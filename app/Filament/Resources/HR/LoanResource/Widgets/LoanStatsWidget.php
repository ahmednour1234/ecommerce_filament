<?php

namespace App\Filament\Resources\HR\LoanResource\Widgets;

use App\Repositories\HR\LoanRepository;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoanStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $repository = app(LoanRepository::class);
        
        $total = \App\Models\HR\Loan::count();
        $active = \App\Models\HR\Loan::where('status', 'active')->count();
        $closed = \App\Models\HR\Loan::where('status', 'closed')->count();

        return [
            Stat::make(tr('stats.total_loans', [], null, 'dashboard') ?: 'Total Loans', $total)
                ->icon('heroicon-o-banknotes')
                ->color('primary'),
            Stat::make(tr('stats.active_loans', [], null, 'dashboard') ?: 'Active Loans', $active)
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),
            Stat::make(tr('stats.closed_loans', [], null, 'dashboard') ?: 'Closed Loans', $closed)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
