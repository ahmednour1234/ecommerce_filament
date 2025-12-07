<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Root categories
        $electronics = Category::updateOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and gadgets',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $clothing = Category::updateOrCreate(
            ['slug' => 'clothing'],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $home = Category::updateOrCreate(
            ['slug' => 'home-garden'],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Sub-categories
        Category::updateOrCreate(
            ['slug' => 'smartphones'],
            [
                'name' => 'Smartphones',
                'parent_id' => $electronics->id,
                'description' => 'Mobile phones and accessories',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'laptops'],
            [
                'name' => 'Laptops',
                'parent_id' => $electronics->id,
                'description' => 'Portable computers',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'mens-clothing'],
            [
                'name' => "Men's Clothing",
                'parent_id' => $clothing->id,
                'description' => 'Clothing for men',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'womens-clothing'],
            [
                'name' => "Women's Clothing",
                'parent_id' => $clothing->id,
                'description' => 'Clothing for women',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}

