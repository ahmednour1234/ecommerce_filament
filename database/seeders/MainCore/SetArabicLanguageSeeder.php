<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Setting;
use App\Models\MainCore\UserPreference;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetArabicLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder sets Arabic (ar) as the default language for the entire system.
     * It updates all aspects of the system to use Arabic by default.
     */
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Setting Arabic as default language for the entire system...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        // 1. Set Arabic as default language and remove default from others
        $arabic = Language::where('code', 'ar')->first();

        if (!$arabic) {
            $this->command->error('❌ Arabic language not found! Please run LanguageSeeder first.');
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

        // 4. Create user preferences for users that don't have one
        $usersWithoutPreferences = User::whereDoesntHave('preferences')->get();
        $createdCount = 0;
        foreach ($usersWithoutPreferences as $user) {
            UserPreference::create([
                'user_id' => $user->id,
                'language_id' => $arabic->id,
            ]);
            $createdCount++;
        }
        if ($createdCount > 0) {
            $this->command->info("✓ Created {$createdCount} user preferences with Arabic language.");
        }

        // 5. Note about sessions - they will use Arabic on next request
        $this->command->info('✓ Active sessions will use Arabic locale on next request.');

        // 6. Update fallback locale setting (if exists)
        Setting::updateOrCreate(
            ['key' => 'app.fallback_locale'],
            [
                'value' => 'ar',
                'group' => 'app',
                'type' => 'string',
                'is_public' => false,
                'autoload' => true,
            ]
        );
        $this->command->info('✓ Fallback locale setting updated to Arabic.');

        // 7. Clear all caches to ensure settings are reloaded
        \Illuminate\Support\Facades\Cache::flush();
        $this->command->info('✓ All caches cleared to reload settings.');

        // 8. Set application locale for current execution
        app()->setLocale('ar');
        session(['locale' => 'ar']);
        $this->command->info('✓ Application locale set to Arabic for current session.');

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✓ Arabic language has been set as default for the entire system!');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $this->command->warn('⚠ IMPORTANT: To complete the setup, update your .env file:');
        $this->command->line('   APP_LOCALE=ar');
        $this->command->line('   APP_FALLBACK_LOCALE=ar');
        $this->command->line('   APP_FAKER_LOCALE=ar_SA');
        $this->command->newLine();
        $this->command->info('Then run: php artisan config:clear');
    }
}

