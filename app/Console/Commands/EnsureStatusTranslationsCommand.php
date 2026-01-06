<?php

namespace App\Console\Commands;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Console\Command;

class EnsureStatusTranslationsCommand extends Command
{
    protected $signature = 'translations:ensure-status';

    protected $description = 'Ensure dashboard.stats.status translations exist in database';

    public function handle(): int
    {
        $this->info('Ensuring status translations exist...');

        // Get all active languages
        $languages = Language::where('is_active', true)->get();

        if ($languages->isEmpty()) {
            $this->error('No active languages found!');
            return Command::FAILURE;
        }

        // Define the translations
        $translations = [
            'dashboard.stats.status.completed' => [
                'en' => 'completed',
                'ar' => 'مكتملة',
            ],
            'dashboard.stats.status.cancelled' => [
                'en' => 'cancelled',
                'ar' => 'ملغاة',
            ],
            'dashboard.stats.status.pending' => [
                'en' => 'pending',
                'ar' => 'معلقة',
            ],
        ];

        $inserted = 0;
        $updated = 0;

        foreach ($translations as $key => $langTranslations) {
            foreach ($languages as $language) {
                $code = $language->code;
                
                // Only create translation if it exists for this language
                if (isset($langTranslations[$code])) {
                    $existing = Translation::where('key', $key)
                        ->where('language_id', $language->id)
                        ->where('group', 'dashboard')
                        ->first();

                    if ($existing) {
                        if ($existing->value !== $langTranslations[$code]) {
                            $existing->update(['value' => $langTranslations[$code]]);
                            $updated++;
                            $this->line("Updated: {$key} ({$code})");
                        }
                    } else {
                        Translation::create([
                            'key' => $key,
                            'language_id' => $language->id,
                            'group' => 'dashboard',
                            'value' => $langTranslations[$code],
                        ]);
                        $inserted++;
                        $this->line("Inserted: {$key} ({$code})");
                    }
                }
            }
        }

        // Clear cache
        app(\App\Services\MainCore\TranslationService::class)->clearCache();

        $this->info("Done! Inserted: {$inserted}, Updated: {$updated}");
        return Command::SUCCESS;
    }
}

