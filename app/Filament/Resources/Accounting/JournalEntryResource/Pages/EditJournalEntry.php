<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Enums\Accounting\JournalEntryStatus;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => 
                    !$this->record->is_posted && 
                    JournalEntryStatus::from($this->record->status ?? JournalEntryStatus::DRAFT->value)->canBeDeleted() &&
                    (auth()->user()?->can('journal_entries.delete') ?? false)
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load lines data for the ExcelGridTable
        $entry = $this->record;
        $data['lines'] = $entry->lines->map(function ($line) {
            return [
                'id' => $line->id,
                'account_id' => $line->account_id,
                'description' => $line->description,
                'debit' => $line->debit,
                'credit' => $line->credit,
                'branch_id' => $line->branch_id,
                'cost_center_id' => $line->cost_center_id,
                'project_id' => $line->project_id ?? null,
                'currency_id' => $line->currency_id ?? null,
                'exchange_rate' => $line->exchange_rate ?? 1,
                'amount' => $line->amount ?? null,
                'base_amount' => $line->base_amount ?? null,
                'reference' => $line->reference ?? null,
            ];
        })->toArray();
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent editing if posted
        if ($this->record->is_posted) {
            throw new \Exception(trans_dash('accounting.validation.cannot_edit_posted', 'Cannot edit a posted entry. Create a reversal entry instead.'));
        }
        
        $status = JournalEntryStatus::from($this->record->status ?? JournalEntryStatus::DRAFT->value);
        if (!$status->canBeEdited()) {
            throw new \Exception(trans_dash('accounting.validation.cannot_edit_status', 'Cannot edit entry in current status.'));
        }
        
        // Validate balance before saving
        $lines = $data['lines'] ?? [];
        $totalDebits = 0;
        $totalCredits = 0;
        
        foreach ($lines as $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
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
        
        if (abs($totalDebits - $totalCredits) >= 0.01) {
            throw new \Exception(trans_dash('accounting.validation.entries_not_balanced', 'Journal entry is not balanced. Total debits must equal total credits.'));
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $entry = $this->record;
        $lines = $this->data['lines'] ?? [];
        
        // Delete existing lines
        $entry->lines()->delete();
        
        // Create new lines
        foreach ($lines as $line) {
            if (isset($line['account_id'])) {
                $lineData = [
                    'account_id' => $line['account_id'],
                    'debit' => (float) ($line['debit'] ?? 0),
                    'credit' => (float) ($line['credit'] ?? 0),
                    'description' => $line['description'] ?? null,
                    'branch_id' => $line['branch_id'] ?? $entry->branch_id,
                    'cost_center_id' => $line['cost_center_id'] ?? $entry->cost_center_id,
                    'project_id' => $line['project_id'] ?? null,
                    'currency_id' => $line['currency_id'] ?? null,
                    'exchange_rate' => (float) ($line['exchange_rate'] ?? 1),
                    'amount' => isset($line['amount']) ? (float) $line['amount'] : null,
                    'base_amount' => isset($line['base_amount']) ? (float) $line['base_amount'] : null,
                    'reference' => $line['reference'] ?? null,
                ];
                
                // Calculate base_amount if not provided
                if (empty($lineData['base_amount']) && $lineData['amount'] && $lineData['exchange_rate']) {
                    $lineData['base_amount'] = $lineData['amount'] * $lineData['exchange_rate'];
                } elseif (empty($lineData['base_amount'])) {
                    $lineData['base_amount'] = $lineData['debit'] > 0 ? $lineData['debit'] : $lineData['credit'];
                }
                
                $entry->lines()->create($lineData);
            }
        }
    }
}

