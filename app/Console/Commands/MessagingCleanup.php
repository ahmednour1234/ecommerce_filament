<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class MessagingCleanup extends Command
{
    protected $signature = 'messaging:cleanup';

    protected $description = 'Drop all messaging module tables';

    public function handle(): int
    {
        if (!$this->confirm('Are you sure you want to drop all messaging tables? This will delete all data!')) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $this->info('Dropping messaging tables...');

        $tables = [
            'sms_message_recipients',
            'sms_messages',
            'message_contacts',
            'contact_messages',
            'sms_templates',
            'sms_settings',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                $this->info("✅ Dropped table: {$table}");
            } else {
                $this->line("ℹ️  Table does not exist: {$table}");
            }
        }

        $this->newLine();
        $this->info('✅ Cleanup completed.');

        return self::SUCCESS;
    }
}
