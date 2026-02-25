<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MessagingInstall extends Command
{
    protected $signature = 'messaging:install';

    protected $description = 'Run migrations and seed the Messaging module data';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('Installing Messaging Module...');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        $this->info('Running migrations...');
        try {
            $this->call('migrate', [
                '--path' => 'database/migrations/2026_01_31_000001_create_message_contacts_table.php',
                '--force' => true,
            ]);
            $this->call('migrate', [
                '--path' => 'database/migrations/2026_01_31_000002_create_sms_messages_table.php',
                '--force' => true,
            ]);
            $this->call('migrate', [
                '--path' => 'database/migrations/2026_01_31_000003_create_sms_message_recipients_table.php',
                '--force' => true,
            ]);
            $this->call('migrate', [
                '--path' => 'database/migrations/2026_01_31_000004_create_contact_messages_table.php',
                '--force' => true,
            ]);
            $this->call('migrate', [
                '--path' => 'database/migrations/2026_01_31_000005_create_sms_templates_table.php',
                '--force' => true,
            ]);
            $this->call('migrate', [
                '--path' => 'database/migrations/2026_01_31_000006_create_sms_settings_table.php',
                '--force' => true,
            ]);
            $this->info('✅ Migrations completed successfully.');
        } catch (\Exception $e) {
            $this->error('❌ Migrations failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Seeding Messaging module data...');
        try {
            $this->call('db:seed', [
                '--class' => 'Database\\Seeders\\Messaging\\MessagingSeeder',
                '--force' => true,
            ]);
            $this->info('✅ Seeding completed successfully.');
        } catch (\Exception $e) {
            $this->error('❌ Seeding failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('Messaging module installed successfully ✅');
        $this->info('═══════════════════════════════════════════════════════');

        return self::SUCCESS;
    }
}
