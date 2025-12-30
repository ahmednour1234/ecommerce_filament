<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Enums\Accounting\JournalEntryStatus;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Period;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = $data['status'] ?? JournalEntryStatus::DRAFT->value;
        
        // Set default fiscal year and period if not provided
        if (empty($data['fiscal_year_id'])) {
            $fiscalYear = FiscalYear::getActive();
            if ($fiscalYear) {
                $data['fiscal_year_id'] = $fiscalYear->id;
            }
        }
        
        if (empty($data['period_id']) && isset($data['entry_date'])) {
            $period = Period::getForDate(\Carbon\Carbon::parse($data['entry_date']));
            if ($period) {
                $data['period_id'] = $period->id;
                if (empty($data['fiscal_year_id'])) {
                    $data['fiscal_year_id'] = $period->fiscal_year_id;
                }
            }
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

    protected function afterCreate(): void
    {
        $entry = $this->record;
        $lines = $this->data['lines'] ?? [];
        
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

