<?php

namespace App\Jobs\Recruitment;

use App\Services\Recruitment\ContractAlertsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateContractAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ContractAlertsService $alertsService): void
    {
        $alertsService->clearCache();
        $alertsService->getAllAlerts();
    }
}
