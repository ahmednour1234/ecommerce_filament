<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Accounting\CurrencyConversionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExchangeRateController extends Controller
{
    protected CurrencyConversionService $conversionService;

    public function __construct(CurrencyConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    /**
     * Get exchange rate for a currency on a specific date
     */
    public function getRate(Request $request): JsonResponse
    {
        $request->validate([
            'currency_id' => 'required|integer|exists:currencies,id',
            'date' => 'nullable|date',
        ]);

        $currencyId = $request->input('currency_id');
        $date = $request->input('date') ? new \DateTime($request->input('date')) : new \DateTime();

        $rate = $this->conversionService->getExchangeRate($currencyId, $date);

        return response()->json([
            'success' => true,
            'rate' => $rate,
        ]);
    }
}

