<?php

/**
 * Simple script to ensure dashboard.stats.status translations exist
 * Run this via: php artisan tinker < ensure_status_translations.php
 * Or copy-paste the code below into tinker
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;

echo "Ensuring status translations exist...\n";

// Get all active languages
$languages = Language::where('is_active', true)->get();

if ($languages->isEmpty()) {
    echo "ERROR: No active languages found!\n";
    exit(1);
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
                    echo "Updated: {$key} ({$code})\n";
                }
            } else {
                Translation::create([
                    'key' => $key,
                    'language_id' => $language->id,
                    'group' => 'dashboard',
                    'value' => $langTranslations[$code],
                ]);
                $inserted++;
                echo "Inserted: {$key} ({$code})\n";
            }
        }
    }
}

// Clear cache
app(\App\Services\MainCore\TranslationService::class)->clearCache();

echo "Done! Inserted: {$inserted}, Updated: {$updated}\n";

