<?php

namespace App\Console\Commands;

use App\Services\ComplaintNotificationService;
use Illuminate\Console\Command;

class CheckComplaintNotifications extends Command
{
    protected $signature = 'complaints:check-notifications';
    protected $description = 'Check for overdue complaints and create notifications';

    public function handle(ComplaintNotificationService $service): int
    {
        $this->info('Checking for overdue complaints...');
        
        $service->checkOverdueComplaints();
        
        $this->info('Notifications check completed.');
        
        return Command::SUCCESS;
    }
}
