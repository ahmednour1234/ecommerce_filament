<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Enums\Accounting\JournalEntryStatus;
use App\Services\Accounting\JournalEntryService;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditJournalEntry extends BaseEditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getJournalEntryService(): JournalEntryService
    {
        return app(JournalEntryService::class);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => 
                    !$this->record->is_posted && 
                    JournalEntryStatus::from($this->record->status ?? JournalEntryStatus::DRAFT->value)->canBeDeleted() &&
                    (auth()->user()?->can('journal_entries.delete') ?? false)
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load lines data using service transformation
        $entry = $this->record->load('lines');
        $data['lines'] = $this->getJournalEntryService()->transformLinesForDisplay($entry->lines);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate balance before saving
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

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Use service to update the entry (with efficient line updates)
        return $this->getJournalEntryService()->update($record, $data);
    }
}

