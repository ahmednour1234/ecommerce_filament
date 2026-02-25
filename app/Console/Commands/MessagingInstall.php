<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MessagingInstall extends Command
{
    protected $signature = 'messaging:install {--fresh : Drop all tables and re-run migrations}';

    protected $description = 'Run migrations and seed the Messaging module data';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('Installing Messaging Module...');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        if ($this->option('fresh')) {
            $this->info('Dropping existing messaging tables...');
            try {
                \Illuminate\Support\Facades\Schema::dropIfExists('sms_message_recipients');
                \Illuminate\Support\Facades\Schema::dropIfExists('sms_messages');
                \Illuminate\Support\Facades\Schema::dropIfExists('message_contacts');
                \Illuminate\Support\Facades\Schema::dropIfExists('contact_messages');
                \Illuminate\Support\Facades\Schema::dropIfExists('sms_templates');
                \Illuminate\Support\Facades\Schema::dropIfExists('sms_settings');
                $this->info('✅ Existing tables dropped.');
                $this->newLine();
            } catch (\Exception $e) {
                $this->warn('⚠️  Warning: Could not drop some tables: ' . $e->getMessage());
            }
        }

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
            $this->newLine();
            $this->warn('💡 Tip: If tables already exist, run: php artisan messaging:install --fresh');
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
