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

        ];
    }
}

