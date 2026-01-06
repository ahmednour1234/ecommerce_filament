<?php

namespace App\Filament\Widgets;

use App\Services\Accounting\AccountingService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountingSummaryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $accountingService = app(AccountingService::class);

        // Get total assets
        $assets = \App\Models\Accounting\Account::where('type', 'asset')
            ->where('is_active', true)
            ->get()
            ->sum(fn ($account) => $accountingService->getAccountBalance($account->id));

        // Get total liabilities
        $liabilities = \App\Models\Accounting\Account::where('type', 'liability')
            ->where('is_active', true)
            ->get()
            ->sum(fn ($account) => $accountingService->getAccountBalance($account->id));

        // Get total equity
        $equity = \App\Models\Accounting\Account::where('type', 'equity')
            ->where('is_active', true)
            ->get()
            ->sum(fn ($account) => $accountingService->getAccountBalance($account->id));

        return [
            Stat::make(tr('dashboard.stats.total_assets'), '$' . number_format($assets, 2))
                ->description(tr('dashboard.stats.total_assets_description'))
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make(tr('dashboard.stats.total_liabilities'), '$' . number_format($liabilities, 2))
                ->description(tr('dashboard.stats.total_liabilities_description'))
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make(tr('dashboard.stats.total_equity'), '$' . number_format($equity, 2))
                ->description(tr('dashboard.stats.total_equity_description'))
                ->color('info')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}

