<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Currency;
use App\Models\MainCore\CurrencyRate;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $usd = Currency::updateOrCreate(
            ['code' => 'USD'],
            [
                'name'       => 'US Dollar',
                'symbol'     => '$',
                'precision'  => 2,
                'is_default' => true,
                'is_active'  => true,
            ]
        );

        $egp = Currency::updateOrCreate(
            ['code' => 'EGP'],
            [
                'name'       => 'Egyptian Pound',
                'symbol'     => 'ج.م',
                'precision'  => 2,
                'is_default' => false,
                'is_active'  => true,
            ]
        );

        // مثال rate واحد
        CurrencyRate::updateOrCreate(
            [
                'base_currency_id'   => $usd->id,
                'target_currency_id' => $egp->id,
                'valid_from'         => now()->startOfDay(),
            ],
            [
                'rate'   => 50.00, // عدّلها حسب الواقع
                'source' => 'manual',
            ]
        );
    }
}
