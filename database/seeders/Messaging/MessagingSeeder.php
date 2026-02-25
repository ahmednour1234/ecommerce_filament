<?php

namespace Database\Seeders\Messaging;

use Illuminate\Database\Seeder;

class MessagingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Seeding Messaging Module...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $seeders = [
            MessagingPermissionsSeeder::class,
            MessagingRoleSeeder::class,
            MessagingTranslationsSeeder::class,
            SmsSettingsSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $seederName = class_basename($seeder);
            $this->command->info("Running {$seederName}...");
            
            try {
                $this->call($seeder);
                $this->command->info("✅ {$seederName} completed successfully.");
            } catch (\Exception $e) {
                $this->command->error("❌ {$seederName} failed: " . $e->getMessage());
            }
            
            $this->command->newLine();
        }

        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✓ Messaging module seeding completed');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
