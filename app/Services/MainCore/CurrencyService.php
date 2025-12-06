<?php

namespace App\Services\MainCore;

use App\Models\MainCore\Currency;
use App\Models\MainCore\CurrencyRate;
use Illuminate\Support\Facades\Cache;
use NumberFormatter;

class CurrencyService
{
    public function defaultCurrency(): ?Currency
    {
        return Cache::remember('maincore.currency.default', now()->addDay(), function () {
            return Currency::where('is_default', true)->first();
        });
    }

    public function findByCode(string $code): ?Currency
    {
        return Cache::remember("maincore.currency.{$code}", now()->addDay(), function () use ($code) {
            return Currency::where('code', strtoupper($code))->first();
        });
    }

    public function convert(float $amount, string $fromCode, string $toCode): float
    {
        $from = $this->findByCode($fromCode);
        $to   = $this->findByCode($toCode);

        if (!$from || !$to || $from->id === $to->id) {
            return $amount;
        }

        /** @var CurrencyRate|null $rate */
        $rate = CurrencyRate::query()
            ->where('base_currency_id', $from->id)
            ->where('target_currency_id', $to->id)
            ->orderByDesc('valid_from')
            ->first();

        if (!$rate) {
            return $amount;
        }

        return round($amount * $rate->rate, $to->precision);
    }

    public function format(float $amount, ?string $code = null, ?string $locale = null, bool $withSymbol = true): string
    {
        $code ??= $this->defaultCurrency()?->code ?? 'USD';
        $currency = $this->findByCode($code);
        $locale ??= app()->getLocale() === 'ar' ? 'ar_EG' : 'en_US';

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        if ($currency) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $currency->precision);
            $formatted = $formatter->formatCurrency($amount, $currency->code);

            if (!$withSymbol) {
                // نشيل الرمز ونسيب الرقم بس
                $formatted = preg_replace('/[^\d\.,\-]+/u', '', $formatted);
            }

            return $formatted;
        }

        return number_format($amount, 2) . ($withSymbol ? " {$code}" : '');
    }
}
