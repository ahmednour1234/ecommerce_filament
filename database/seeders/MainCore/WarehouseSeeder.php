<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Branch;
use App\Models\MainCore\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MAIN')->first();

        $warehouses = [
            [
                'code' => 'WH-MAIN',
                'name' => 'Main Warehouse',
                'branch_id' => $mainBranch?->id,
                'address' => '123 Warehouse Street, Industrial Area',
                'phone' => '+1234567890',
                'email' => 'warehouse@company.com',
                'is_active' => true,
            ],
            [
                'code' => 'WH-NORTH',
                'name' => 'North Warehouse',
                'branch_id' => Branch::where('code', 'NORTH')->first()?->id,
                'address' => '456 North Industrial Zone',
                'phone' => '+1234567891',
                'email' => 'north-warehouse@company.com',
                'is_active' => true,
            ],
            [
                'code' => 'WH-SOUTH',
                'name' => 'South Warehouse',
                'branch_id' => Branch::where('code', 'SOUTH')->first()?->id,
                'address' => '789 South Distribution Center',
                'phone' => '+1234567892',
                'email' => 'south-warehouse@company.com',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }
    }
}

