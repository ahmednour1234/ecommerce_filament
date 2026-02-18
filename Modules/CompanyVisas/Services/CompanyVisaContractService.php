<?php

namespace Modules\CompanyVisas\Services;

use Illuminate\Support\Facades\DB;
use Modules\CompanyVisas\Entities\CompanyVisaContract;

class CompanyVisaContractService
{
    public static function generateContractNo(): string
    {
        $year = now()->format('Y');
        $lastContract = CompanyVisaContract::where('contract_no', 'like', "CON-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastContract) {
            $lastNumber = (int) substr($lastContract->contract_no, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('CON-%s-%04d', $year, $nextNumber);
    }

    public static function linkWorkers(CompanyVisaContract $contract, array $workerIds): void
    {
        DB::transaction(function () use ($contract, $workerIds) {
            $existingIds = $contract->workers()->pluck('worker_id')->toArray();
            $newIds = array_diff($workerIds, $existingIds);

            foreach ($newIds as $workerId) {
                $contract->workers()->attach($workerId);
            }

            $contract->refresh();
        });
    }

    public static function unlinkWorker(CompanyVisaContract $contract, int $workerId): void
    {
        DB::transaction(function () use ($contract, $workerId) {
            $contract->workers()->detach($workerId);
            $contract->refresh();
        });
    }

    public static function updateCounts(CompanyVisaContract $contract): void
    {
        $contract->updateLinkedWorkersCount();
    }
}
