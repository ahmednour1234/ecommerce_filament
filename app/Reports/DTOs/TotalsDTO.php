<?php

namespace App\Reports\DTOs;

/**
 * Totals Data Transfer Object
 * Standardizes totals structure for reports
 */
class TotalsDTO
{
    public float $totalDebit = 0.0;
    public float $totalCredit = 0.0;
    public float $totalAmount = 0.0;
    public float $openingBalance = 0.0;
    public float $closingBalance = 0.0;
    public array $customTotals = [];

    public function __construct(array $data = [])
    {
        $this->totalDebit = (float) ($data['total_debit'] ?? $data['totalDebit'] ?? 0);
        $this->totalCredit = (float) ($data['total_credit'] ?? $data['totalCredit'] ?? 0);
        $this->totalAmount = (float) ($data['total_amount'] ?? $data['totalAmount'] ?? 0);
        $this->openingBalance = (float) ($data['opening_balance'] ?? $data['openingBalance'] ?? 0);
        $this->closingBalance = (float) ($data['closing_balance'] ?? $data['closingBalance'] ?? 0);
        
        // Store any custom totals
        $standardKeys = [
            'total_debit', 'totalDebit', 'total_credit', 'totalCredit',
            'total_amount', 'totalAmount', 'opening_balance', 'openingBalance',
            'closing_balance', 'closingBalance'
        ];
        
        foreach ($data as $key => $value) {
            if (!in_array($key, $standardKeys)) {
                $this->customTotals[$key] = $value;
            }
        }
    }

    /**
     * Get net balance (debit - credit)
     */
    public function getNetBalance(): float
    {
        return $this->totalDebit - $this->totalCredit;
    }

    /**
     * Check if totals are balanced (debit == credit)
     */
    public function isBalanced(float $tolerance = 0.01): bool
    {
        return abs($this->totalDebit - $this->totalCredit) < $tolerance;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'total_debit' => $this->totalDebit,
            'total_credit' => $this->totalCredit,
            'total_amount' => $this->totalAmount,
            'opening_balance' => $this->openingBalance,
            'closing_balance' => $this->closingBalance,
            'net_balance' => $this->getNetBalance(),
            'is_balanced' => $this->isBalanced(),
            ...$this->customTotals,
        ];
    }
}

