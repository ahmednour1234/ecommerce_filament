<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HousingUpdateStatuses extends Command
{
    protected $signature = 'housing:update-statuses';

    protected $description = 'Update housing statuses and translations';

    public function handle(): int
    {
        $this->info('Updating housing statuses...');
        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\Housing\\HousingStatusSeeder',
            '--force' => true,
        ]);

        $this->info('Updating housing translations...');
        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\Housing\\HousingTranslationsSeeder',
            '--force' => true,
        ]);

        $this->info('✅ Housing statuses and translations updated successfully.');

        return self::SUCCESS;
    }
}
