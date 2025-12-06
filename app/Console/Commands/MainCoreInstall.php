<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MainCoreInstall extends Command
{
    /**
     * اسم الأمر اللي هتستخدمه في Artisan.
     */
    protected $signature = 'maincore:install';

    /**
     * وصف مختصر للأمر.
     */
    protected $description = 'Run migrations and seed the MainCore core data';

    /**
     * تنفيذ الأمر.
     */
    public function handle(): int
    {
        $this->info('Running migrations...');
        $this->call('migrate', ['--force' => true]);

        $this->info('Seeding MainCore data...');
        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\MainCore\\MainCoreSeeder',
            '--force' => true,
        ]);

        $this->info('MainCore installed successfully ✅');

        return self::SUCCESS;
    }
}
