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
            'navigation.biometric_devices' => ['en' => 'Biometric Devices', 'ar' => 'أجهزة البصمة الحيوية'],
            'sidebar.biometric_devices' => ['en' => 'Biometric Devices', 'ar' => 'أجهزة البصمة الحيوية'],
            'navigation.biometric_attendances' => ['en' => 'Biometric Attendance Logs', 'ar' => 'سجلات الحضور الحيوية'],
            'sidebar.biometric_attendances' => ['en' => 'Biometric Attendance Logs', 'ar' => 'سجلات الحضور الحيوية'],

            'fields.device' => ['en' => 'Device', 'ar' => 'الجهاز'],

            'tables.biometric_devices.name' => ['en' => 'Device Name', 'ar' => 'اسم الجهاز'],
            'tables.biometric_devices.serial_number' => ['en' => 'Serial Number', 'ar' => 'الرقم التسلسلي'],
            'tables.biometric_devices.ip_address' => ['en' => 'IP Address', 'ar' => 'عنوان IP'],
            'tables.biometric_devices.api_key' => ['en' => 'API Key', 'ar' => 'مفتاح API'],
            'tables.biometric_devices.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.biometric_devices.attendances_count' => ['en' => 'Logs Count', 'ar' => 'عدد السجلات'],
            'tables.biometric_devices.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],

            'tables.biometric_attendances.device' => ['en' => 'Device', 'ar' => 'الجهاز'],
            'tables.biometric_attendances.user_id' => ['en' => 'User ID', 'ar' => 'رقم المستخدم'],
            'tables.biometric_attendances.attended_at' => ['en' => 'Attended At', 'ar' => 'وقت الحضور'],
            'tables.biometric_attendances.state' => ['en' => 'State', 'ar' => 'الحالة'],
            'tables.biometric_attendances.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.biometric_attendances.ip_address' => ['en' => 'IP Address', 'ar' => 'عنوان IP'],
            'tables.biometric_attendances.processed' => ['en' => 'Processed', 'ar' => 'تم المعالجة'],
            'tables.biometric_attendances.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
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
