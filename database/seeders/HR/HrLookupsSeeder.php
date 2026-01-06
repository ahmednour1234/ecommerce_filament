<?php

namespace Database\Seeders\HR;

use App\Models\HR\BloodType;
use App\Models\HR\IdentityType;
use Illuminate\Database\Seeder;

class HrLookupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds default blood types and identity types.
     */
    public function run(): void
    {
        $this->command->info('Seeding HR lookups...');

        // Seed Blood Types
        $bloodTypes = [
            ['name' => 'A+', 'code' => 'A+', 'active' => true],
            ['name' => 'A-', 'code' => 'A-', 'active' => true],
            ['name' => 'B+', 'code' => 'B+', 'active' => true],
            ['name' => 'B-', 'code' => 'B-', 'active' => true],
            ['name' => 'AB+', 'code' => 'AB+', 'active' => true],
            ['name' => 'AB-', 'code' => 'AB-', 'active' => true],
            ['name' => 'O+', 'code' => 'O+', 'active' => true],
            ['name' => 'O-', 'code' => 'O-', 'active' => true],
        ];

        $bloodTypesCreated = 0;
        foreach ($bloodTypes as $bloodType) {
            BloodType::updateOrCreate(
                ['code' => $bloodType['code']],
                $bloodType
            );
            $bloodTypesCreated++;
        }

        $this->command->info("✓ Blood types seeded: {$bloodTypesCreated}");

        // Seed Identity Types
        $identityTypes = [
            ['name' => 'National ID', 'active' => true],
            ['name' => 'Passport', 'active' => true],
            ['name' => 'Residence Permit', 'active' => true],
            ['name' => 'Driver License', 'active' => true],
            ['name' => 'Other', 'active' => true],
        ];

        $identityTypesCreated = 0;
        foreach ($identityTypes as $identityType) {
            IdentityType::updateOrCreate(
                ['name' => $identityType['name']],
                $identityType
            );
            $identityTypesCreated++;
        }

        $this->command->info("✓ Identity types seeded: {$identityTypesCreated}");
        $this->command->info('✓ HR lookups seeding completed');
    }
}

