<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\CostCenter;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    public function run(): void
    {
        // Root cost centers
        $sales = CostCenter::updateOrCreate(
            ['code' => 'SALES'],
            [
                'name' => 'Sales Department',
                'type' => 'department',
                'description' => 'Sales and marketing operations',
                'is_active' => true,
            ]
        );

        $operations = CostCenter::updateOrCreate(
            ['code' => 'OPS'],
            [
                'name' => 'Operations',
                'type' => 'department',
                'description' => 'Operations and logistics',
                'is_active' => true,
            ]
        );

        $admin = CostCenter::updateOrCreate(
            ['code' => 'ADMIN'],
            [
                'name' => 'Administration',
                'type' => 'department',
                'description' => 'Administrative functions',
                'is_active' => true,
            ]
        );

        // Sub cost centers
        CostCenter::updateOrCreate(
            ['code' => 'SALES-MKT'],
            [
                'name' => 'Marketing',
                'type' => 'sub-department',
                'parent_id' => $sales->id,
                'description' => 'Marketing activities',
                'is_active' => true,
            ]
        );

        CostCenter::updateOrCreate(
            ['code' => 'OPS-WAREHOUSE'],
            [
                'name' => 'Warehouse',
                'type' => 'sub-department',
                'parent_id' => $operations->id,
                'description' => 'Warehouse operations',
                'is_active' => true,
            ]
        );
    }
}

