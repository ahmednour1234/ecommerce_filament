<?php

namespace App\Support;

use App\Services\MainCore\CurrencyService;
use App\Services\Accounting\CurrencyConversionService;

/**
 * Money Helper Class
 * Provides static methods for money formatting and currency conversion
 */
class Money
{
    protected static ?CurrencyService $currencyService = null;
    protected static ?CurrencyConversionService $conversionService = null;

    /**
     * Get currency service instance
     */
    protected static function currencyService(): CurrencyService
    {
        if (self::$currencyService === null) {
            self::$currencyService = app(CurrencyService::class);
        }
        return self::$currencyService;
    }

    /**
     * Get currency conversion service instance
     */
    protected static function conversionService(): CurrencyConversionService
    {
        if (self::$conversionService === null) {
            self::$conversionService = app(CurrencyConversionService::class);
        }
        return self::$conversionService;
    }

    /**
     * Format amount with currency
     *
     * @param float|int|string $amount
     * @param string|int|null $currency Currency code or ID
     * @param bool $withSymbol Include currency symbol
     * @return string
     */
    public static function format($amount, $currency = null, bool $withSymbol = true): string
    {
        $amount = (float) $amount;
        
        // If currency is an ID, get the code
        if (is_int($currency)) {
            $currencyModel = \App\Models\MainCore\Currency::find($currency);
            $currency = $currencyModel?->code ?? null;
        }
        
        return self::currencyService()->format($amount, $currency, null, $withSymbol);
    }

    /**
     * Convert amount to base currency
     *
     * @param float|int|string $amount
     * @param int $currencyId
     * @param \DateTime|string $date
     * @return float
     */
    public static function toBase($amount, int $currencyId, $date): float
    {
        $amount = (float) $amount;
        
        if ($date instanceof \DateTime) {
            $dateObj = $date;
        } else {
            $dateObj = new \DateTime($date);
        }
        
        return self::conversionService()->convertToBase($amount, $currencyId, $dateObj);
    }

    /**
     * Format amount without currency symbol
     *
     * @param float|int|string $amount
     * @param string|int|null $currency
     * @return string
     */
    public static function formatWithoutSymbol($amount, $currency = null): string
    {
        return self::format($amount, $currency, false);
    }

    /**
     * Get default currency code
     *
     * @return string
     */
    public static function defaultCurrencyCode(): string
    {
        $currency = self::currencyService()->defaultCurrency();
        return $currency?->code ?? 'USD';
    }

    /**
     * Get default currency ID
     *
     * @return int|null
     */
    public static function defaultCurrencyId(): ?int
    {
        $currency = self::currencyService()->defaultCurrency();
        return $currency?->id ?? null;
    }
}

