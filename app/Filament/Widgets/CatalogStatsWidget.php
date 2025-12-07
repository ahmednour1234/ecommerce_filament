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
            Stat::make('Total Products', Number::format($totalProducts))
                ->description("{$activeProducts} active products")
                ->descriptionIcon('heroicon-o-cube')
                ->color('success')
                ->icon('heroicon-o-archive-box'),

            Stat::make('Total Categories', Number::format($totalCategories))
                ->description('Active categories')
                ->descriptionIcon('heroicon-o-folder')
                ->color('info')
                ->icon('heroicon-o-squares-2x2'),

            Stat::make('Total Brands', Number::format($totalBrands))
                ->description('Active brands')
                ->descriptionIcon('heroicon-o-tag')
                ->color('primary')
                ->icon('heroicon-o-building-storefront'),

            Stat::make('Low Stock Alert', Number::format($lowStockProducts))
                ->description('Products with stock ≤ 10')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('Inventory Value', Number::currency($totalProductValue))
                ->description('Total product inventory value')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}

