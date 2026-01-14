<?php

namespace App\Services\Finance;

use Illuminate\Support\Facades\DB;

class CurrencyConverterService
{
    public function getDefaultCurrencyId(): int
    {
        return (int) DB::table('currencies')->where('is_default', 1)->value('id');
    }

    /**
     * rate = تحويل من base_currency_id -> target_currency_id
     * في جدول currency_rates عندك:
     * base_currency_id, target_currency_id, rate, valid_from
     */
    public function getRate(int $fromCurrencyId, int $toCurrencyId, string $date): float
    {
        if ($fromCurrencyId === $toCurrencyId) {
            return 1.0;
        }

        $rate = DB::table('currency_rates')
            ->where('base_currency_id', $fromCurrencyId)
            ->where('target_currency_id', $toCurrencyId)
            ->whereDate('valid_from', '<=', $date)
            ->orderByDesc('valid_from')
            ->value('rate');

        if ($rate) {
            return (float) $rate;
        }

        // fallback: لو موجود العكس نحسب 1/rate
        $reverse = DB::table('currency_rates')
            ->where('base_currency_id', $toCurrencyId)
            ->where('target_currency_id', $fromCurrencyId)
            ->whereDate('valid_from', '<=', $date)
            ->orderByDesc('valid_from')
            ->value('rate');

        if ($reverse) {
            return 1 / (float) $reverse;
        }

        // لو مفيش سعر => 1 (أو throw حسب سياستك)
        return 1.0;
    }

    public function convert(float $amount, int $fromCurrencyId, int $toCurrencyId, string $date): array
    {
        $rate = $this->getRate($fromCurrencyId, $toCurrencyId, $date);
        return [
            'rate' => $rate,
            'converted' => round($amount * $rate, 2),
        ];
    }
}
