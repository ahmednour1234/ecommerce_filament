<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Theme;
use App\Models\MainCore\UserPreference;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $defaultLanguage = Language::where('is_default', true)->first();
        $defaultTheme = Theme::where('is_default', true)->first();

        if (!$user) {
            return; // Skip if no users exist
        }

        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'language_id' => $defaultLanguage?->id,
                'theme_id' => $defaultTheme?->id,
                'timezone' => 'UTC',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
            ]
        );
    }
}

