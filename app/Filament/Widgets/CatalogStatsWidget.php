<?php

namespace App\Filament\Widgets;

use App\Models\Catalog\Brand;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class CatalogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::where('is_active', true)->count();
        $totalBrands = Brand::where('is_active', true)->count();

        $activeProducts = Product::where('is_active', true)
            ->where('type', 'product')
            ->count();

        $lowStockProducts = Product::where('is_active', true)
            ->where('track_inventory', true)
            ->where('stock_quantity', '<=', 10)
            ->count();

        $totalProductValue = Product::where('is_active', true)
            ->where('type', 'product')
            ->get()
            ->sum(fn ($product) => $product->price * $product->stock_quantity);

        return [

        ];
    }
}

