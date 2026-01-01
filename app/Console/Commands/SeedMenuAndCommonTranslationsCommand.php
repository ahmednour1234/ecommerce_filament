<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedMenuAndCommonTranslationsCommand extends Command
{
    protected $signature = 'translations:seed-menu-and-common';

    protected $description = 'Seed common translation keys (sidebar, actions, common terms, forms, tables)';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('Seeding Menu and Common Translations');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        $seeders = [
            \Database\Seeders\MainCore\SidebarTranslationsSeeder::class,
            \Database\Seeders\MainCore\ActionsTranslationsSeeder::class,
            \Database\Seeders\MainCore\CommonTermsSeeder::class,
            \Database\Seeders\MainCore\FormsTranslationsSeeder::class,
            \Database\Seeders\MainCore\TablesTranslationsSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $seederName = class_basename($seeder);
            $this->info("Running {$seederName}...");
            
            try {
                $this->call('db:seed', ['--class' => $seeder]);
                $this->info("✅ {$seederName} completed successfully.");
            } catch (\Exception $e) {
                $this->error("❌ {$seederName} failed: " . $e->getMessage());
            }
            
            $this->newLine();
        }

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('✅ All common translations seeded successfully!');
        $this->info('═══════════════════════════════════════════════════════');

        return Command::SUCCESS;
    }
}

