<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Setting;
use App\Models\MainCore\UserPreference;
use Illuminate\Database\Seeder;

class SetArabicLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder sets Arabic (ar) as the default language for the entire system.
     */
    public function run(): void
    {
        $this->command->info('Setting Arabic as default language for the system...');

        // 1. Set Arabic as default language and remove default from others
        $arabic = Language::where('code', 'ar')->first();
        
        if (!$arabic) {
            $this->command->error('Arabic language not found! Please run LanguageSeeder first.');
            return;
        }

        // Remove default flag from all languages
        Language::query()->update(['is_default' => false]);

        // Set Arabic as default
        $arabic->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->command->info('✓ Arabic language set as default in languages table.');

        // 2. Update app.default_language setting
        Setting::updateOrCreate(
            ['key' => 'app.default_language'],
            [
                'value' => 'ar',
                'group' => 'app',
                'type' => 'string',
                'is_public' => true,
                'autoload' => true,
            ]
        );

        $this->command->info('✓ App default language setting updated to Arabic.');

        // 3. Update all user preferences to use Arabic
        $preferencesToUpdate = UserPreference::where(function ($query) use ($arabic) {
            $query->where('language_id', '!=', $arabic->id)
                  ->orWhereNull('language_id');
        })->get();

        $updatedCount = 0;
        foreach ($preferencesToUpdate as $preference) {
            $preference->update(['language_id' => $arabic->id]);
            $updatedCount++;
        }

        $this->command->info("✓ Updated {$updatedCount} user preferences to Arabic.");

        // 4. Clear cache to ensure settings are reloaded
        \Illuminate\Support\Facades\Cache::flush();
        $this->command->info('✓ Cache cleared to reload settings.');

        $this->command->info('');
        $this->command->info('✓ Arabic language has been set as default for the entire system!');
        $this->command->info('');
        $this->command->warn('⚠ Note: If you want to change the default locale in config/app.php, update APP_LOCALE in your .env file to "ar"');
    }
}

