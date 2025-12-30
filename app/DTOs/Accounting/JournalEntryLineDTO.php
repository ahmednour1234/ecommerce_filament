<?php

namespace App\DTOs\Accounting;

class JournalEntryLineDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $account_id = null,
        public float $debit = 0,
        public float $credit = 0,
        public ?string $description = null,
        public ?int $branch_id = null,
        public ?int $cost_center_id = null,
        public ?int $project_id = null,
        public ?int $currency_id = null,
        public float $exchange_rate = 1,
        public ?float $amount = null,
        public ?float $base_amount = null,
        public ?string $reference = null,
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            account_id: $data['account_id'] ?? null,
            debit: (float) ($data['debit'] ?? 0),
            credit: (float) ($data['credit'] ?? 0),
            description: $data['description'] ?? null,
            branch_id: $data['branch_id'] ?? null,
            cost_center_id: $data['cost_center_id'] ?? null,
            project_id: $data['project_id'] ?? null,
            currency_id: $data['currency_id'] ?? null,
            exchange_rate: (float) ($data['exchange_rate'] ?? 1),
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            base_amount: isset($data['base_amount']) ? (float) $data['base_amount'] : null,
            reference: $data['reference'] ?? null,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'description' => $this->description,
            'branch_id' => $this->branch_id,
            'cost_center_id' => $this->cost_center_id,
            'project_id' => $this->project_id,
            'currency_id' => $this->currency_id,
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount,
            'base_amount' => $this->base_amount,
            'reference' => $this->reference,
        ];
    }

    /**
     * Check if line is valid (has either debit or credit, not both)
     */
    public function isValid(): bool
    {
        return ($this->debit > 0 && $this->credit == 0) || 
               ($this->credit > 0 && $this->debit == 0);
    }

    /**
     * Get amount in base currency
     */
    public function getBaseAmount(): float
    {
        if ($this->base_amount !== null) {
            return $this->base_amount;
        }

        if ($this->amount && $this->exchange_rate) {
            return $this->amount * $this->exchange_rate;
        }

        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    /**
     * Validate line data
     */
    public function validate(): array
    {
        $errors = [];

        if (!$this->account_id) {
            $errors[] = trans_dash('accounting.validation.account_required', 'Account is required');
        }

        if (!$this->isValid()) {
            $errors[] = trans_dash('accounting.validation.debit_or_credit_required', 'Either debit or credit must be entered, not both');
        }

        if ($this->debit == 0 && $this->credit == 0) {
            $errors[] = trans_dash('accounting.validation.amount_required', 'Debit or credit amount is required');
        }

        if ($this->currency_id && $this->amount && $this->exchange_rate <= 0) {
            $errors[] = trans_dash('accounting.validation.exchange_rate_required', 'Exchange rate is required for foreign currency');
        }

        return $errors;
    }
}

