<?php

namespace Database\Seeders\MainCore;

use Illuminate\Database\Seeder;

class MainCoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            ThemeSeeder::class,
            SettingSeeder::class,
            PaymentSeeder::class,
            NotificationSeeder::class,
            ShippingSeeder::class,
        ]);
    }
}
