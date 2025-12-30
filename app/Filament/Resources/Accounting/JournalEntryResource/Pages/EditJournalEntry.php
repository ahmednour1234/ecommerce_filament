<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Enums\Accounting\JournalEntryStatus;
use App\Services\Accounting\JournalEntryService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected JournalEntryService $journalEntryService;

    public function boot(): void
    {
        parent::boot();
        $this->journalEntryService = app(JournalEntryService::class);
    }

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
        // Load lines data using service transformation
        $entry = $this->record->load('lines');
        $data['lines'] = $this->journalEntryService->transformLinesForDisplay($entry->lines);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Service will handle validation
        return $data;
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Use service to update the entry (with efficient line updates)
        return $this->journalEntryService->update($record, $data);
    }
}

