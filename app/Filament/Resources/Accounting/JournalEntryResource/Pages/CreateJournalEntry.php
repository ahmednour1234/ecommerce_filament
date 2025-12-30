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
        // Service will handle validation and defaults
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Use service to create the entry
        return $this->getJournalEntryService()->create($data);
    }
}

