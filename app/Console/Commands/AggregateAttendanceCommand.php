<?php

namespace App\Console\Commands;

use App\Services\HR\AttendanceService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AggregateAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:aggregate-attendance 
                            {--date= : Date to aggregate (Y-m-d format, defaults to yesterday)}
                            {--all : Aggregate all dates from logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate attendance logs into daily attendance summaries';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService): int
    {
        $this->info('Starting attendance aggregation...');

        if ($this->option('all')) {
            // Aggregate all dates that have logs but no attendance_days
            $this->info('Aggregating all dates with logs...');
            // This would require a more complex query to find dates with logs but no summaries
            $this->warn('--all option not fully implemented. Use --date for specific dates.');
            return Command::FAILURE;
        }

        $date = $this->option('date') 
            ? Carbon::parse($this->option('date'))
            : Carbon::yesterday();

        $this->info("Aggregating attendance for date: {$date->format('Y-m-d')}");

        try {
            $attendanceService->aggregateForDate($date->format('Y-m-d'));
            
            $this->info("✓ Successfully aggregated attendance for {$date->format('Y-m-d')}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Error aggregating attendance: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}

