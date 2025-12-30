<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Services\Accounting\JournalEntryService;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getJournalEntryService(): JournalEntryService
    {
        return app(JournalEntryService::class);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate balance before creating
        $lines = $data['lines'] ?? [];
        $validation = $this->getJournalEntryService()->validateBalance($lines);
        
        if (!$validation['is_balanced']) {
            \Filament\Notifications\Notification::make()
                ->title(trans_dash('accounting.validation.entries_not_balanced', 'Journal entry is not balanced'))
                ->body(trans_dash(
                    'accounting.validation.balance_details',
                    'Total debits: :debits, Total credits: :credits, Difference: :difference',
                    [
                        'debits' => number_format($validation['total_debits'], 2),
                        'credits' => number_format($validation['total_credits'], 2),
                        'difference' => number_format($validation['difference'], 2),
                    ]
                ))
                ->danger()
                ->send();
            
            throw new \Illuminate\Validation\ValidationException(
                \Illuminate\Support\Facades\Validator::make([], [])
                    ->errors()
                    ->add('lines', trans_dash('accounting.validation.entries_not_balanced', 'Entries are not balanced'))
            );
        }
        
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Use service to create the entry
        return $this->getJournalEntryService()->create($data);
    }
}

