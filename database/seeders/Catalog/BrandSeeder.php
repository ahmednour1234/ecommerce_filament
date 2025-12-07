<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'description' => 'Premium technology brand',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung',
                'description' => 'Leading electronics manufacturer',
                'is_active' => true,
            ],
            [
                'name' => 'Nike',
                'description' => 'Athletic footwear and apparel',
                'is_active' => true,
            ],
            [
                'name' => 'Sony',
                'description' => 'Consumer electronics and entertainment',
                'is_active' => true,
            ],
            [
                'name' => 'Microsoft',
                'description' => 'Software and hardware technology',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($brand['name'])],
                $brand
            );
        }
    }
}

