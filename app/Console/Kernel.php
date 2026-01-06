<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string<\Illuminate\Console\Command>>
     */
    protected $commands = [
        // لما تعمل Commands مخصصة ضيفها هنا مثلاً:
         \App\Console\Commands\MainCoreInstall::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // هنا بتحط أي cron jobs
        // مثال:
        // $schedule->command('maincore:sync-currencies')->daily();
        
        // Aggregate attendance daily at 1 AM
        $schedule->command('hr:aggregate-attendance')
            ->dailyAt('01:00')
            ->timezone(config('app.timezone', 'UTC'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
