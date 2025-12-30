<?php

namespace App\Services\Accounting;

use App\Models\MainCore\Currency;
use App\Models\MainCore\CurrencyRate;
use App\Services\MainCore\CurrencyService;

class CurrencyConversionService
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get exchange rate for a currency on a specific date
     */
    public function getExchangeRate(int $currencyId, \DateTime $date): float
    {
        $currency = Currency::find($currencyId);
        if (!$currency) {
            return 1.0;
        }
        
        $baseCurrency = $this->currencyService->defaultCurrency();
        if (!$baseCurrency || $currency->id === $baseCurrency->id) {
            return 1.0;
        }
        
        // Get rate for the date
        $rate = CurrencyRate::where('base_currency_id', $baseCurrency->id)
            ->where('target_currency_id', $currencyId)
            ->where('valid_from', '<=', $date)
            ->orderByDesc('valid_from')
            ->first();
        
        if ($rate) {
            return (float) $rate->rate;
        }
        
        // Try inverse rate
        $inverseRate = CurrencyRate::where('base_currency_id', $currencyId)
            ->where('target_currency_id', $baseCurrency->id)
            ->where('valid_from', '<=', $date)
            ->orderByDesc('valid_from')
            ->first();
        
        if ($inverseRate) {
            return 1.0 / (float) $inverseRate->rate;
        }
        
        return 1.0; // Default to 1 if no rate found
    }

    /**
     * Convert amount to base currency
     */
    public function convertToBase(float $amount, int $currencyId, \DateTime $date): float
    {
        if ($amount == 0) {
            return 0.0;
        }
        
        $rate = $this->getExchangeRate($currencyId, $date);
        return round($amount * $rate, 2);
    }

    /**
     * Convert amount from base currency to target currency
     */
    public function convertFromBase(float $amount, int $toCurrencyId, \DateTime $date): float
    {
        if ($amount == 0) {
            return 0.0;
        }
        
        $rate = $this->getExchangeRate($toCurrencyId, $date);
        if ($rate == 0) {
            return 0.0;
        }
        
        return round($amount / $rate, 2);
    }
}

