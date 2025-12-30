<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use App\Enums\Accounting\JournalEntryStatus;
use App\Services\Accounting\JournalEntryService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected JournalEntryService $journalEntryService;

    public function boot(): void
    {
        parent::boot();
        $this->journalEntryService = app(JournalEntryService::class);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Service will handle validation and defaults
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Use service to create the entry
        return $this->journalEntryService->create($data);
    }
}

