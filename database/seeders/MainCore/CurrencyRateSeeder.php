<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Currency;
use App\Models\MainCore\CurrencyRate;
use Illuminate\Database\Seeder;

class CurrencyRateSeeder extends Seeder
{
    public function run(): void
    {
        $usd = Currency::where('code', 'USD')->first();
        $egp = Currency::where('code', 'EGP')->first();

        if (!$usd || !$egp) {
            return; // Skip if currencies don't exist
        }

        // USD to EGP rate
        CurrencyRate::updateOrCreate(
            [
                'base_currency_id' => $usd->id,
                'target_currency_id' => $egp->id,
                'valid_from' => now()->startOfDay(),
            ],
            [
                'rate' => 50.00,
                'source' => 'manual',
            ]
        );

        // EGP to USD rate (inverse)
        CurrencyRate::updateOrCreate(
            [
                'base_currency_id' => $egp->id,
                'target_currency_id' => $usd->id,
                'valid_from' => now()->startOfDay(),
            ],
            [
                'rate' => 0.02, // 1 EGP = 0.02 USD
                'source' => 'manual',
            ]
        );
    }
}

