<?php

namespace App\Console\Commands;

use App\Models\Recruitment\RecruitmentContract;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CalculateRecruitmentContractDates extends Command
{
    protected $signature = 'recruitment:calculate-dates';

    protected $description = 'Calculate arrival_date, trial_end_date, and contract_end_date for existing recruitment contracts';

    public function handle()
    {
        $this->info('Starting to calculate dates for recruitment contracts...');

        $contracts = RecruitmentContract::where(function ($query) {
                $query->whereNull('arrival_date')
                    ->where(function ($q) {
                        $q->whereNotNull('visa_date')
                          ->orWhere('status', 'received');
                    });
            })
            ->get();

        $bar = $this->output->createProgressBar($contracts->count());
        $bar->start();

        $updated = 0;

        foreach ($contracts as $contract) {
            $arrivalDate = null;

            if ($contract->visa_date) {
                $arrivalDate = Carbon::parse($contract->visa_date);
            } elseif ($contract->status === 'received') {
                $receivedLog = $contract->statusLogs()
                    ->where('new_status', 'received')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($receivedLog) {
                    $arrivalDate = $receivedLog->status_date 
                        ? Carbon::parse($receivedLog->status_date)
                        : Carbon::parse($receivedLog->created_at);
                }
            }

            if ($arrivalDate) {
                $contract->update([
                    'arrival_date' => $arrivalDate->toDateString(),
                    'trial_end_date' => $arrivalDate->copy()->addDays(90)->toDateString(),
                    'contract_end_date' => $arrivalDate->copy()->addYears(2)->toDateString(),
                ]);
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Updated {$updated} contracts out of {$contracts->count()} total.");

        return Command::SUCCESS;
    }
}
