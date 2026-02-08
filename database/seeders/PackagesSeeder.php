<?php

namespace Database\Seeders;

use App\Models\MainCore\Country;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Test Packages...');

        $country = Country::where('is_active', true)->first();
        $user = User::first();

        if (!$country || !$user) {
            $this->command->error('Missing required data! Please seed countries and users first.');
            return;
        }

        $durationTypes = ['day', 'month', 'year'];
        $statuses = ['active', 'inactive'];

        $packages = [];

        for ($i = 1; $i <= 10; $i++) {
            $type = $i <= 4 ? 'rental' : ($i <= 7 ? 'recruitment' : 'service_transfer');
            $durationType = $durationTypes[array_rand($durationTypes)];
            $status = $statuses[array_rand($statuses)];

            $duration = $durationType === 'day' ? rand(7, 30) : ($durationType === 'month' ? rand(1, 12) : rand(1, 2));

            $basePrice = rand(2000, 8000);
            $externalCosts = rand(500, 2000);
            $workerSalary = rand(1000, 3000);
            $govFees = rand(500, 1500);
            $taxPercent = 15;

            $package = Package::create([
                'type' => $type,
                'name' => 'Package ' . $i . ' - ' . ucfirst($type),
                'description' => 'Test package ' . $i . ' for ' . $type . ' services',
                'country_id' => $country->id,
                'status' => $status,
                'duration_type' => $durationType,
                'duration' => $duration,
                'base_price' => $basePrice,
                'external_costs' => $externalCosts,
                'worker_salary' => $workerSalary,
                'gov_fees' => $govFees,
                'tax_percent' => $taxPercent,
                'created_by' => $user->id,
            ]);

            $packages[] = $package;
        }

        $this->command->info('✓ Created ' . count($packages) . ' Packages');
    }
}
