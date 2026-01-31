<?php

namespace App\Providers;

use App\Services\Rental\FinanceGateway;
use App\Services\Rental\BranchTransactionFinanceGateway;
use Illuminate\Support\ServiceProvider;

class RentalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FinanceGateway::class, BranchTransactionFinanceGateway::class);
    }

    public function boot(): void
    {
        //
    }
}
