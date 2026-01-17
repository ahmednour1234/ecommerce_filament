<?php

namespace Database\Seeders\Recruitment;

use App\Models\Recruitment\Nationality;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Nationalities...');

        $nationalities = [
            ['name_ar' => 'سعودي', 'name_en' => 'Saudi', 'code' => 'SA', 'is_active' => true],
            ['name_ar' => 'مصري', 'name_en' => 'Egyptian', 'code' => 'EG', 'is_active' => true],
            ['name_ar' => 'أردني', 'name_en' => 'Jordanian', 'code' => 'JO', 'is_active' => true],
            ['name_ar' => 'سوري', 'name_en' => 'Syrian', 'code' => 'SY', 'is_active' => true],
            ['name_ar' => 'لبناني', 'name_en' => 'Lebanese', 'code' => 'LB', 'is_active' => true],
            ['name_ar' => 'فلسطيني', 'name_en' => 'Palestinian', 'code' => 'PS', 'is_active' => true],
            ['name_ar' => 'عراقي', 'name_en' => 'Iraqi', 'code' => 'IQ', 'is_active' => true],
            ['name_ar' => 'يمني', 'name_en' => 'Yemeni', 'code' => 'YE', 'is_active' => true],
            ['name_ar' => 'سوداني', 'name_en' => 'Sudanese', 'code' => 'SD', 'is_active' => true],
            ['name_ar' => 'تونسي', 'name_en' => 'Tunisian', 'code' => 'TN', 'is_active' => true],
            ['name_ar' => 'جزائري', 'name_en' => 'Algerian', 'code' => 'DZ', 'is_active' => true],
            ['name_ar' => 'مغربي', 'name_en' => 'Moroccan', 'code' => 'MA', 'is_active' => true],
            ['name_ar' => 'كويتي', 'name_en' => 'Kuwaiti', 'code' => 'KW', 'is_active' => true],
            ['name_ar' => 'إماراتي', 'name_en' => 'Emirati', 'code' => 'AE', 'is_active' => true],
            ['name_ar' => 'قطري', 'name_en' => 'Qatari', 'code' => 'QA', 'is_active' => true],
            ['name_ar' => 'بحريني', 'name_en' => 'Bahraini', 'code' => 'BH', 'is_active' => true],
            ['name_ar' => 'عُماني', 'name_en' => 'Omani', 'code' => 'OM', 'is_active' => true],
            ['name_ar' => 'باكستاني', 'name_en' => 'Pakistani', 'code' => 'PK', 'is_active' => true],
            ['name_ar' => 'هندي', 'name_en' => 'Indian', 'code' => 'IN', 'is_active' => true],
            ['name_ar' => 'بنغلاديشي', 'name_en' => 'Bangladeshi', 'code' => 'BD', 'is_active' => true],
            ['name_ar' => 'فلبيني', 'name_en' => 'Filipino', 'code' => 'PH', 'is_active' => true],
            ['name_ar' => 'إندونيسي', 'name_en' => 'Indonesian', 'code' => 'ID', 'is_active' => true],
            ['name_ar' => 'سريلانكي', 'name_en' => 'Sri Lankan', 'code' => 'LK', 'is_active' => true],
            ['name_ar' => 'نيبالي', 'name_en' => 'Nepalese', 'code' => 'NP', 'is_active' => true],
            ['name_ar' => 'أفغاني', 'name_en' => 'Afghan', 'code' => 'AF', 'is_active' => true],
            ['name_ar' => 'أثيوبي', 'name_en' => 'Ethiopian', 'code' => 'ET', 'is_active' => true],
            ['name_ar' => 'إريتري', 'name_en' => 'Eritrean', 'code' => 'ER', 'is_active' => true],
            ['name_ar' => 'أوغندي', 'name_en' => 'Ugandan', 'code' => 'UG', 'is_active' => true],
            ['name_ar' => 'كينيّ', 'name_en' => 'Kenyan', 'code' => 'KE', 'is_active' => true],
            ['name_ar' => 'تنزاني', 'name_en' => 'Tanzanian', 'code' => 'TZ', 'is_active' => true],
        ];

        $created = 0;
        foreach ($nationalities as $nationality) {
            Nationality::updateOrCreate(
                ['code' => $nationality['code']],
                $nationality
            );
            $created++;
        }

        $this->command->info("✓ Nationalities seeded: {$created}");
    }
}
