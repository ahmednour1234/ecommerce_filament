<?php

namespace Database\Seeders\Rental;

use App\Models\MainCore\Country;
use Illuminate\Database\Seeder;

class RentalCountriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Adding rental countries if not exists...');

        $rentalCountries = [
            ['iso2' => 'BD', 'iso3' => 'BGD', 'name_en' => 'Bangladesh', 'name_ar' => 'بنجلادش'],
            ['iso2' => 'PH', 'iso3' => 'PHL', 'name_en' => 'Philippines', 'name_ar' => 'الفلبين'],
            ['iso2' => 'LK', 'iso3' => 'LKA', 'name_en' => 'Sri Lanka', 'name_ar' => 'سريلانكا'],
            ['iso2' => 'UG', 'iso3' => 'UGA', 'name_en' => 'Uganda', 'name_ar' => 'أوغندا'],
            ['iso2' => 'ET', 'iso3' => 'ETH', 'name_en' => 'Ethiopia', 'name_ar' => 'إثيوبيا'],
            ['iso2' => 'KE', 'iso3' => 'KEN', 'name_en' => 'Kenya', 'name_ar' => 'كينيا'],
            ['iso2' => 'BI', 'iso3' => 'BDI', 'name_en' => 'Burundi', 'name_ar' => 'بوروندي'],
        ];

        foreach ($rentalCountries as $country) {
            Country::updateOrCreate(
                ['iso2' => $country['iso2']],
                [
                    'iso3' => $country['iso3'],
                    'name' => [
                        'en' => $country['name_en'],
                        'ar' => $country['name_ar'],
                    ],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✓ Rental countries added/updated');
    }
}
