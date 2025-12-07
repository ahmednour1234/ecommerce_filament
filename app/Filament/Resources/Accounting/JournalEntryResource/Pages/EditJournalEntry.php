<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate balance before saving
        $lines = $data['lines'] ?? [];
        $totalDebits = collect($lines)->sum('debit');
        $totalCredits = collect($lines)->sum('credit');
        
        if (abs($totalDebits - $totalCredits) >= 0.01) {
            throw new \Exception('Journal entry is not balanced. Total debits must equal total credits.');
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync lines
        $entry = $this->record;
        $lines = $this->data['lines'] ?? [];
        
        // Delete existing lines
        $entry->lines()->delete();
        
        // Create new lines
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

