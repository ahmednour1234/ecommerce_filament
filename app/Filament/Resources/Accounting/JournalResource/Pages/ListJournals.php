<?php

namespace App\Filament\Resources\Accounting\JournalResource\Pages;

use App\Filament\Resources\Accounting\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

