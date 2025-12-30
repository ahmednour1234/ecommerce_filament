<?php

namespace App\Services\Accounting;

use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Period;
use App\Enums\Accounting\JournalEntryStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class JournalEntryService
{
    /**
     * Create a new journal entry
     */
    public function create(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            // Set defaults
            $data['user_id'] = $data['user_id'] ?? auth()->id();
            $data['status'] = $data['status'] ?? JournalEntryStatus::DRAFT->value;

            // Set default fiscal year and period if not provided
            if (empty($data['fiscal_year_id'])) {
                $fiscalYear = FiscalYear::getActive();
                if ($fiscalYear) {
                    $data['fiscal_year_id'] = $fiscalYear->id;
                }
            }

            if (empty($data['period_id']) && isset($data['entry_date'])) {
                $period = Period::getForDate(Carbon::parse($data['entry_date']));
                if ($period) {
                    $data['period_id'] = $period->id;
                    if (empty($data['fiscal_year_id'])) {
                        $data['fiscal_year_id'] = $period->fiscal_year_id;
                    }
                }
            }

            // Validate balance before creating
            $lines = $data['lines'] ?? [];
            $validation = $this->validateBalance($lines);
            if (!$validation['is_balanced']) {
                throw new \Exception(trans_dash(
                    'accounting.validation.entries_not_balanced',
                    'Journal entry is not balanced. Total debits must equal total credits. Difference: :difference',
                    ['difference' => number_format($validation['difference'], 2)]
                ));
            }

            // Create the entry
            $entry = JournalEntry::create([
                'journal_id' => $data['journal_id'],
                'entry_number' => $data['entry_number'],
                'entry_date' => $data['entry_date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'branch_id' => $data['branch_id'],
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'user_id' => $data['user_id'],
                'status' => $data['status'],
                'fiscal_year_id' => $data['fiscal_year_id'] ?? null,
                'period_id' => $data['period_id'] ?? null,
            ]);

            // Create lines
            $this->createLines($entry, $lines);

            return $entry->fresh(['lines']);
        });
    }

    /**
     * Update an existing journal entry
     */
    public function update(JournalEntry $entry, array $data): JournalEntry
    {
        // Prevent editing if posted
        if ($entry->is_posted) {
            throw new \Exception(trans_dash(
                'accounting.validation.cannot_edit_posted',
                'Cannot edit a posted entry. Create a reversal entry instead.'
            ));
        }

        $status = JournalEntryStatus::from($entry->status ?? JournalEntryStatus::DRAFT->value);
        if (!$status->canBeEdited()) {
            throw new \Exception(trans_dash(
                'accounting.validation.cannot_edit_status',
                'Cannot edit entry in current status.'
            ));
        }

        return DB::transaction(function () use ($entry, $data) {
            // Update fiscal year and period if entry_date changed
            if (isset($data['entry_date']) && $data['entry_date'] != $entry->entry_date) {
                $period = Period::getForDate(Carbon::parse($data['entry_date']));
                if ($period) {
                    $data['period_id'] = $period->id;
                    $data['fiscal_year_id'] = $period->fiscal_year_id;
                }
            }

            // Validate balance before updating
            $lines = $data['lines'] ?? [];
            $validation = $this->validateBalance($lines);
            if (!$validation['is_balanced']) {
                throw new \Exception(trans_dash(
                    'accounting.validation.entries_not_balanced',
                    'Journal entry is not balanced. Total debits must equal total credits. Difference: :difference',
                    ['difference' => number_format($validation['difference'], 2)]
                ));
            }

            // Update the entry
            $entry->update([
                'journal_id' => $data['journal_id'] ?? $entry->journal_id,
                'entry_number' => $data['entry_number'] ?? $entry->entry_number,
                'entry_date' => $data['entry_date'] ?? $entry->entry_date,
                'reference' => $data['reference'] ?? $entry->reference,
                'description' => $data['description'] ?? $entry->description,
                'branch_id' => $data['branch_id'] ?? $entry->branch_id,
                'cost_center_id' => $data['cost_center_id'] ?? $entry->cost_center_id,
                'fiscal_year_id' => $data['fiscal_year_id'] ?? $entry->fiscal_year_id,
                'period_id' => $data['period_id'] ?? $entry->period_id,
                'status' => $data['status'] ?? $entry->status,
            ]);

            // Update lines efficiently
            $this->updateLines($entry, $lines);

            return $entry->fresh(['lines']);
        });
    }

    /**
     * Validate journal entry balance
     */
    public function validateBalance(array $lines): array
    {
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($lines as $line) {
            // Determine debit/credit based on type field or amounts
            $type = $line['type'] ?? ($line['debit'] > 0 ? 'debit' : 'credit');
            $debit = $type === 'debit' ? (float) ($line['debit'] ?? 0) : 0;
            $credit = $type === 'credit' ? (float) ($line['credit'] ?? 0) : 0;

            // If type not set, use existing debit/credit values
            if (!isset($line['type'])) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
            }

            $baseAmount = (float) ($line['base_amount'] ?? 0);

            if ($baseAmount > 0) {
                if ($debit > 0) {
                    $totalDebits += $baseAmount;
                } else {
                    $totalCredits += $baseAmount;
                }
            } else {
                $totalDebits += $debit;
                $totalCredits += $credit;
            }
        }

        $difference = abs($totalDebits - $totalCredits);

        return [
            'is_balanced' => $difference < 0.01,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => $difference,
        ];
    }

    /**
     * Transform lines from frontend format to storage format
     */
    public function transformLinesForStorage(array $lines): array
    {
        $transformed = [];

        foreach ($lines as $line) {
            if (empty($line['account_id'])) {
                continue; // Skip lines without account
            }

            // Determine debit/credit based on type field or amounts
            $type = $line['type'] ?? ($line['debit'] > 0 ? 'debit' : 'credit');
            $debit = $type === 'debit' ? (float) ($line['debit'] ?? 0) : 0;
            $credit = $type === 'credit' ? (float) ($line['credit'] ?? 0) : 0;

            // If type not set, use existing debit/credit values
            if (!isset($line['type'])) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
            }

            $lineData = [
                'account_id' => (int) $line['account_id'],
                'debit' => $debit,
                'credit' => $credit,
                'description' => $line['description'] ?? null,
                'branch_id' => isset($line['branch_id']) ? (int) $line['branch_id'] : null,
                'cost_center_id' => isset($line['cost_center_id']) ? (int) $line['cost_center_id'] : null,
                'project_id' => isset($line['project_id']) ? (int) $line['project_id'] : null,
                'currency_id' => isset($line['currency_id']) ? (int) $line['currency_id'] : null,
                'exchange_rate' => (float) ($line['exchange_rate'] ?? 1),
                'amount' => isset($line['amount']) ? (float) $line['amount'] : ($debit > 0 ? $debit : $credit),
                'base_amount' => isset($line['base_amount']) ? (float) $line['base_amount'] : null,
                'reference' => $line['reference'] ?? null,
            ];

            // Calculate base_amount if not provided
            if (empty($lineData['base_amount'])) {
                $amount = $lineData['amount'] ?? ($debit > 0 ? $debit : $credit);
                $exchangeRate = $lineData['exchange_rate'];
                $defaultCurrencyId = app(\App\Services\MainCore\CurrencyService::class)->defaultCurrency()?->id;
                
                if ($lineData['currency_id'] && $lineData['currency_id'] != $defaultCurrencyId) {
                    $lineData['base_amount'] = round($amount * $exchangeRate, 2);
                } else {
                    $lineData['base_amount'] = $amount;
                }
            }

            $transformed[] = $lineData;
        }

        return $transformed;
    }

    /**
     * Transform lines from storage format to display format
     */
    public function transformLinesForDisplay(Collection $lines): array
    {
        return $lines->map(function ($line) {
            return [
                'id' => $line->id,
                'type' => $line->debit > 0 ? 'debit' : 'credit',
                'account_id' => $line->account_id,
                'description' => $line->description,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
                'branch_id' => $line->branch_id,
                'cost_center_id' => $line->cost_center_id,
                'project_id' => $line->project_id ?? null,
                'currency_id' => $line->currency_id ?? null,
                'exchange_rate' => (float) ($line->exchange_rate ?? 1),
                'amount' => $line->amount ? (float) $line->amount : null,
                'base_amount' => $line->base_amount ? (float) $line->base_amount : null,
                'reference' => $line->reference ?? null,
            ];
        })->toArray();
    }

    /**
     * Create lines for a journal entry
     */
    protected function createLines(JournalEntry $entry, array $lines): void
    {
        $transformedLines = $this->transformLinesForStorage($lines);

        foreach ($transformedLines as $lineData) {
            // Use entry's branch_id and cost_center_id as defaults if not provided
            if (empty($lineData['branch_id'])) {
                $lineData['branch_id'] = $entry->branch_id;
            }
            if (empty($lineData['cost_center_id'])) {
                $lineData['cost_center_id'] = $entry->cost_center_id;
            }

            $entry->lines()->create($lineData);
        }
    }

    /**
     * Update lines efficiently (update existing, create new, delete removed)
     */
    protected function updateLines(JournalEntry $entry, array $lines): void
    {
        $transformedLines = $this->transformLinesForStorage($lines);

        // Get existing line IDs
        $existingLineIds = $entry->lines()->pluck('id')->toArray();
        $newLineIds = [];

        foreach ($transformedLines as $lineData) {
            $lineId = $lineData['id'] ?? null;

            // Use entry's branch_id and cost_center_id as defaults if not provided
            if (empty($lineData['branch_id'])) {
                $lineData['branch_id'] = $entry->branch_id;
            }
            if (empty($lineData['cost_center_id'])) {
                $lineData['cost_center_id'] = $entry->cost_center_id;
            }

            // Remove id from lineData for create/update
            unset($lineData['id']);

            if ($lineId && in_array($lineId, $existingLineIds)) {
                // Update existing line
                $entry->lines()->where('id', $lineId)->update($lineData);
                $newLineIds[] = $lineId;
            } else {
                // Create new line
                $newLine = $entry->lines()->create($lineData);
                $newLineIds[] = $newLine->id;
            }
        }

        // Delete lines that are no longer present
        $linesToDelete = array_diff($existingLineIds, $newLineIds);
        if (!empty($linesToDelete)) {
            $entry->lines()->whereIn('id', $linesToDelete)->delete();
        }
    }
}

