<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'code' => 'MAIN',
                'name' => 'Main Branch',
                'address' => '123 Main Street, City Center',
                'phone' => '+1234567890',
                'email' => 'main@company.com',
                'status' => 'active',
            ],
            [
                'code' => 'NORTH',
                'name' => 'North Branch',
                'address' => '456 North Avenue, North District',
                'phone' => '+1234567891',
                'email' => 'north@company.com',
                'status' => 'active',
            ],
            [
                'code' => 'SOUTH',
                'name' => 'South Branch',
                'address' => '789 South Boulevard, South District',
                'phone' => '+1234567892',
                'email' => 'south@company.com',
                'status' => 'active',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['code' => $branch['code']],
                $branch
            );
        }
    }
}

