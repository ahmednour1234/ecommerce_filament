<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Models\Accounting\JournalEntry;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        
        // Validate balance before saving
        $lines = $data['lines'] ?? [];
        $totalDebits = collect($lines)->sum('debit');
        $totalCredits = collect($lines)->sum('credit');
        
        if (abs($totalDebits - $totalCredits) >= 0.01) {
            throw new \Exception('Journal entry is not balanced. Total debits must equal total credits.');
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // The lines are automatically saved via the relationship
        // But we need to ensure they're saved with the correct branch_id and cost_center_id
        $entry = $this->record;
        $lines = $this->data['lines'] ?? [];
        
        foreach ($lines as $line) {
            if (isset($line['account_id'])) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                    'branch_id' => $line['branch_id'] ?? $entry->branch_id,
                    'cost_center_id' => $line['cost_center_id'] ?? $entry->cost_center_id,
                ]);
            }
        }
    }
}

