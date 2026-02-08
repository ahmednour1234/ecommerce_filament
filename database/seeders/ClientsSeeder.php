<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClientsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Test Clients...');

        $maritalStatuses = ['single', 'married', 'divorced', 'widowed'];
        $classifications = ['new', 'vip', 'blocked'];
        $housingTypes = ['villa', 'building', 'apartment', 'farm'];
        $cities = ['الرياض', 'جدة', 'الدمام', 'المدينة المنورة', 'الخبر'];

        $clients = [];

        for ($i = 1; $i <= 20; $i++) {
            $maritalStatus = $maritalStatuses[array_rand($maritalStatuses)];
            $classification = $classifications[array_rand($classifications)];
            $housingType = $housingTypes[array_rand($housingTypes)];
            $city = $cities[array_rand($cities)];

            $nationalId = '1' . str_pad($i, 9, '0', STR_PAD_LEFT);
            $mobile = '05' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

            $client = Client::create([
                'name_ar' => 'عميل ' . $i,
                'name_en' => 'Client ' . $i,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'mobile2' => rand(0, 1) ? '05' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT) : null,
                'email' => rand(0, 1) ? 'client' . $i . '@example.com' : null,
                'birth_date' => Carbon::now()->subYears(rand(25, 65)),
                'marital_status' => $maritalStatus,
                'classification' => $classification,
                'building_no' => rand(1000, 9999),
                'street_name' => 'شارع ' . $i,
                'city_name' => $city,
                'district_name' => 'حي ' . $i,
                'postal_code' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'additional_no' => rand(0, 1) ? rand(1000, 9999) : null,
                'unit_no' => rand(0, 1) ? rand(1, 100) : null,
                'building_no_en' => rand(1000, 9999),
                'street_name_en' => 'Street ' . $i,
                'city_name_en' => ucfirst($city),
                'district_name_en' => 'District ' . $i,
                'unit_no_en' => rand(0, 1) ? rand(1, 100) : null,
                'full_address_ar' => 'الرياض، حي ' . $i . '، شارع ' . $i,
                'full_address_en' => 'Riyadh, District ' . $i . ', Street ' . $i,
                'housing_type' => $housingType,
                'source' => rand(0, 1) ? 'website' : 'office',
                'office_referral' => rand(0, 1) ? 'Referral ' . $i : null,
            ]);

            $clients[] = $client;
        }

        $this->command->info('✓ Created ' . count($clients) . ' Clients');
    }
}
