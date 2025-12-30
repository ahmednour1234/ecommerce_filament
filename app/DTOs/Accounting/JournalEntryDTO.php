<?php

namespace App\DTOs\Accounting;

use App\Enums\Accounting\JournalEntryStatus;

class JournalEntryDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $journal_id = null,
        public ?string $entry_number = null,
        public ?string $entry_date = null,
        public ?string $reference = null,
        public ?string $description = null,
        public ?int $branch_id = null,
        public ?int $cost_center_id = null,
        public ?int $user_id = null,
        public JournalEntryStatus $status = JournalEntryStatus::DRAFT,
        public bool $is_posted = false,
        public ?int $fiscal_year_id = null,
        public ?int $period_id = null,
        public array $lines = [],
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        $lines = [];
        if (isset($data['lines']) && is_array($data['lines'])) {
            foreach ($data['lines'] as $line) {
                $lines[] = JournalEntryLineDTO::fromArray($line);
            }
        }

        return new self(
            id: $data['id'] ?? null,
            journal_id: $data['journal_id'] ?? null,
            entry_number: $data['entry_number'] ?? null,
            entry_date: $data['entry_date'] ?? null,
            reference: $data['reference'] ?? null,
            description: $data['description'] ?? null,
            branch_id: $data['branch_id'] ?? null,
            cost_center_id: $data['cost_center_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            status: isset($data['status']) ? JournalEntryStatus::from($data['status']) : JournalEntryStatus::DRAFT,
            is_posted: $data['is_posted'] ?? false,
            fiscal_year_id: $data['fiscal_year_id'] ?? null,
            period_id: $data['period_id'] ?? null,
            lines: $lines,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'journal_id' => $this->journal_id,
            'entry_number' => $this->entry_number,
            'entry_date' => $this->entry_date,
            'reference' => $this->reference,
            'description' => $this->description,
            'branch_id' => $this->branch_id,
            'cost_center_id' => $this->cost_center_id,
            'user_id' => $this->user_id,
            'status' => $this->status->value,
            'is_posted' => $this->is_posted,
            'fiscal_year_id' => $this->fiscal_year_id,
            'period_id' => $this->period_id,
            'lines' => array_map(fn($line) => $line->toArray(), $this->lines),
        ];
    }

    /**
     * Calculate total debits
     */
    public function getTotalDebits(): float
    {
        return array_sum(array_map(function ($line) {
            return $line->getBaseAmount() > 0 && $line->debit > 0 ? $line->getBaseAmount() : $line->debit;
        }, $this->lines));
    }

    /**
     * Calculate total credits
     */
    public function getTotalCredits(): float
    {
        return array_sum(array_map(function ($line) {
            return $line->getBaseAmount() > 0 && $line->credit > 0 ? $line->getBaseAmount() : $line->credit;
        }, $this->lines));
    }

    /**
     * Check if entry is balanced
     */
    public function isBalanced(): bool
    {
        $difference = abs($this->getTotalDebits() - $this->getTotalCredits());
        return $difference < 0.01;
    }

    /**
     * Get balance difference
     */
    public function getBalanceDifference(): float
    {
        return abs($this->getTotalDebits() - $this->getTotalCredits());
    }

    /**
     * Validate entry data
     */
    public function validate(): array
    {
        $errors = [];

        if (!$this->journal_id) {
            $errors[] = trans_dash('accounting.validation.journal_required', 'Journal is required');
        }

        if (!$this->entry_date) {
            $errors[] = trans_dash('accounting.validation.entry_date_required', 'Entry date is required');
        }

        if (!$this->branch_id) {
            $errors[] = trans_dash('accounting.validation.branch_required', 'Branch is required');
        }

        if (empty($this->lines)) {
            $errors[] = trans_dash('accounting.validation.lines_required', 'At least one journal entry line is required');
        }

        if (count($this->lines) < 2) {
            $errors[] = trans_dash('accounting.validation.minimum_two_lines', 'At least two journal entry lines are required');
        }

        // Validate each line
        foreach ($this->lines as $index => $line) {
            $lineErrors = $line->validate();
            foreach ($lineErrors as $error) {
                $errors[] = trans_dash('accounting.validation.line_error', 'Line :index: :error', [
                    'index' => $index + 1,
                    'error' => $error,
                ]);
            }
        }

        // Validate balance
        if (!$this->isBalanced()) {
            $errors[] = trans_dash('accounting.validation.entries_not_balanced', 'Journal entry is not balanced. Difference: :difference', [
                'difference' => number_format($this->getBalanceDifference(), 2),
            ]);
        }

        return $errors;
    }
}

