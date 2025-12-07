<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Brand;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\MainCore\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            $defaultCurrency = Currency::first();
        }

        $smartphonesCategory = Category::where('slug', 'smartphones')->first();
        $laptopsCategory = Category::where('slug', 'laptops')->first();
        $electronicsCategory = Category::where('slug', 'electronics')->first();

        $appleBrand = Brand::where('slug', 'apple')->first();
        $samsungBrand = Brand::where('slug', 'samsung')->first();

        $products = [
            [
                'sku' => 'IPHONE-15-128',
                'name' => 'iPhone 15 128GB',
                'category_id' => $smartphonesCategory?->id ?? $electronicsCategory?->id,
                'brand_id' => $appleBrand?->id,
                'type' => 'product',
                'price' => 999.00,
                'cost' => 750.00,
                'currency_id' => $defaultCurrency?->id,
                'stock_quantity' => 50,
                'track_inventory' => true,
                'is_active' => true,
                'description' => 'Latest iPhone with 128GB storage',
            ],
            [
                'sku' => 'SAMSUNG-S24-256',
                'name' => 'Samsung Galaxy S24 256GB',
                'category_id' => $smartphonesCategory?->id ?? $electronicsCategory?->id,
                'brand_id' => $samsungBrand?->id,
                'type' => 'product',
                'price' => 899.00,
                'cost' => 680.00,
                'currency_id' => $defaultCurrency?->id,
                'stock_quantity' => 30,
                'track_inventory' => true,
                'is_active' => true,
                'description' => 'Samsung flagship smartphone',
            ],
            [
                'sku' => 'MACBOOK-PRO-14',
                'name' => 'MacBook Pro 14"',
                'category_id' => $laptopsCategory?->id ?? $electronicsCategory?->id,
                'brand_id' => $appleBrand?->id,
                'type' => 'product',
                'price' => 1999.00,
                'cost' => 1500.00,
                'currency_id' => $defaultCurrency?->id,
                'stock_quantity' => 20,
                'track_inventory' => true,
                'is_active' => true,
                'description' => 'Professional laptop for creators',
            ],
            [
                'sku' => 'SERVICE-REPAIR',
                'name' => 'Device Repair Service',
                'category_id' => $electronicsCategory?->id,
                'type' => 'service',
                'price' => 99.00,
                'cost' => 50.00,
                'currency_id' => $defaultCurrency?->id,
                'stock_quantity' => 0,
                'track_inventory' => false,
                'is_active' => true,
                'description' => 'Professional device repair service',
            ],
        ];

        foreach ($products as $product) {
            // Generate slug from product name
            $product['slug'] = Str::slug($product['name']);
            
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}

