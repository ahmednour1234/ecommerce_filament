<?php

namespace Tests\Unit\Accounting;

use Tests\TestCase;
use App\Models\MainCore\Currency;
use App\Models\MainCore\CurrencyRate;
use App\Services\Accounting\CurrencyConversionService;
use App\Services\MainCore\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrencyConversionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CurrencyConversionService $conversionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conversionService = app(CurrencyConversionService::class);
    }

    /** @test */
    public function it_returns_rate_of_one_for_base_currency()
    {
        $baseCurrency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_default' => true,
            'is_active' => true,
        ]);
        
        $date = new \DateTime();

        $rate = $this->conversionService->getExchangeRate($baseCurrency->id, $date);

        $this->assertEquals(1.0, $rate);
    }

    /** @test */
    public function it_gets_exchange_rate_for_currency_on_date()
    {
        $baseCurrency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_default' => true,
            'is_active' => true,
        ]);
        
        $eurCurrency = Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'is_active' => true,
        ]);

        $date = new \DateTime('2024-01-15');
        CurrencyRate::create([
            'base_currency_id' => $baseCurrency->id,
            'target_currency_id' => $eurCurrency->id,
            'rate' => 0.85,
            'valid_from' => '2024-01-01',
        ]);

        $rate = $this->conversionService->getExchangeRate($eurCurrency->id, $date);

        $this->assertEquals(0.85, $rate);
    }

    /** @test */
    public function it_uses_latest_rate_before_date()
    {
        $baseCurrency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_default' => true,
            'is_active' => true,
        ]);
        
        $eurCurrency = Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'is_active' => true,
        ]);

        CurrencyRate::create([
            'base_currency_id' => $baseCurrency->id,
            'target_currency_id' => $eurCurrency->id,
            'rate' => 0.80,
            'valid_from' => '2024-01-01',
        ]);

        CurrencyRate::create([
            'base_currency_id' => $baseCurrency->id,
            'target_currency_id' => $eurCurrency->id,
            'rate' => 0.85,
            'valid_from' => '2024-01-10',
        ]);

        $date = new \DateTime('2024-01-15');
        $rate = $this->conversionService->getExchangeRate($eurCurrency->id, $date);

        $this->assertEquals(0.85, $rate);
    }

    /** @test */
    public function it_converts_amount_to_base_currency()
    {
        $baseCurrency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_default' => true,
            'is_active' => true,
        ]);
        
        $eurCurrency = Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'is_active' => true,
        ]);

        $date = new \DateTime('2024-01-15');
        CurrencyRate::create([
            'base_currency_id' => $baseCurrency->id,
            'target_currency_id' => $eurCurrency->id,
            'rate' => 0.85,
            'valid_from' => '2024-01-01',
        ]);

        $amount = 1000.00; // EUR
        $converted = $this->conversionService->convertToBase($amount, $eurCurrency->id, $date);

        $this->assertEquals(850.00, $converted);
    }
}

