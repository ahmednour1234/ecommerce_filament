<?php

namespace Database\Seeders\MainCore;

use Illuminate\Database\Seeder;

class MainCoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            MenuTranslationsSeeder::class, // Must be after LanguageSeeder
            CurrencySeeder::class,
            CurrencyRateSeeder::class, // Must be after CurrencySeeder
            ThemeSeeder::class,
            SettingSeeder::class,
            PaymentSeeder::class,
            NotificationSeeder::class,
            ShippingSeeder::class,
            BranchSeeder::class,
            WarehouseSeeder::class, // Must be after BranchSeeder
            CostCenterSeeder::class,
            UserPreferenceSeeder::class, // Must be after User, Currency, Language, Theme seeders
            DashboardTranslationSeeder::class, // Must be after LanguageSeeder
        ]);
    }
}
