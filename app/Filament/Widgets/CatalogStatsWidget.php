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
            Stat::make(tr('dashboard.stats.total_products'), Number::format($totalProducts))
                ->description("{$activeProducts} " . tr('dashboard.stats.total_products_description'))
                ->descriptionIcon('heroicon-o-cube')
                ->color('success')
                ->icon('heroicon-o-archive-box'),

            Stat::make(tr('dashboard.stats.total_categories'), Number::format($totalCategories))
                ->description(tr('dashboard.stats.total_categories_description'))
                ->descriptionIcon('heroicon-o-folder')
                ->color('info')
                ->icon('heroicon-o-squares-2x2'),

            Stat::make(tr('dashboard.stats.total_brands'), Number::format($totalBrands))
                ->description(tr('dashboard.stats.total_brands_description'))
                ->descriptionIcon('heroicon-o-tag')
                ->color('primary')
                ->icon('heroicon-o-building-storefront'),

            Stat::make(tr('dashboard.stats.low_stock_alert'), Number::format($lowStockProducts))
                ->description(tr('dashboard.stats.low_stock_alert_description'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make(tr('dashboard.stats.inventory_value'), Number::currency($totalProductValue))
                ->description(tr('dashboard.stats.inventory_value_description'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}

