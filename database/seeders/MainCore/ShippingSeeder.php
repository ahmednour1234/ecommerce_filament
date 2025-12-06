<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\ShippingProvider;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        ShippingProvider::updateOrCreate(
            ['code' => 'manual'],
            [
                'name'      => 'Manual / In-house Delivery',
                'config'    => [],
                'is_active' => true,
            ]
        );
    }
}
