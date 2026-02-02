<?php

namespace Database\Seeders\Biometric;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class BiometricTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Biometric translations.');
            return;
        }

        $this->command->info('Creating Biometric module translations...');

        $translations = [
            'navigation.biometric_attendances' => ['en' => 'Biometric Attendance Logs', 'ar' => 'سجلات الحضور الحيوية'],
            'sidebar.biometric_attendances' => ['en' => 'Biometric Attendance Logs', 'ar' => 'سجلات الحضور الحيوية'],

            'tables.biometric_attendances.attended_at' => ['en' => 'Attended At', 'ar' => 'وقت الحضور'],
            'tables.biometric_attendances.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.biometric_devices.attendances_count' => ['en' => 'Logs Count', 'ar' => 'عدد السجلات'],
        ];

        $created = 0;
        foreach ($translations as $key => $values) {
            if (isset($values['en'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $english->id,
                    ],
                    ['value' => $values['en']]
                );
                $created++;
            }

            if (isset($values['ar'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $arabic->id,
                    ],
                    ['value' => $values['ar']]
                );
                $created++;
            }
        }

        $this->command->info("✓ Biometric translations created: {$created} entries");
    }
}
