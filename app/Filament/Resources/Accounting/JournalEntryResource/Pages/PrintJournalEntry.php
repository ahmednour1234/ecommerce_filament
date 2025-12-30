<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use Filament\Resources\Pages\ViewRecord;

class PrintJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected static string $view = 'filament.resources.accounting.journal-entries.print';

    public function getTitle(): string
    {
        return trans_dash('accounting.print_journal_entry', 'Print Journal Entry');
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public function getSubheading(): string | null
    {
        return $this->record->entry_number ?? null;
    }
}

