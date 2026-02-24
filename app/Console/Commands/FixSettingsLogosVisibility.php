<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\MainCore\Setting;

class FixSettingsLogosVisibility extends Command
{
    protected $signature = 'settings:fix-logos-visibility';
    protected $description = 'Fix visibility for all logo files in settings';

    public function handle()
    {
        $this->info('Fixing logo files visibility...');

        // Fix logos in settings table
        $settings = Setting::whereNotNull('logo')->get();
        $fixed = 0;

        foreach ($settings as $setting) {
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                try {
                    Storage::disk('public')->setVisibility($setting->logo, 'public');
                    $fixed++;
                    $this->line("Fixed: {$setting->logo}");
                } catch (\Exception $e) {
                    $this->error("Failed to fix: {$setting->logo} - {$e->getMessage()}");
                }
            }
        }

        // Fix all files in settings/logos directory
        $files = Storage::disk('public')->allFiles('settings/logos');
        foreach ($files as $file) {
            try {
                Storage::disk('public')->setVisibility($file, 'public');
                $this->line("Fixed: {$file}");
                $fixed++;
            } catch (\Exception $e) {
                $this->error("Failed to fix: {$file} - {$e->getMessage()}");
            }
        }

        $this->info("Fixed visibility for {$fixed} files.");
        return Command::SUCCESS;
    }
}
