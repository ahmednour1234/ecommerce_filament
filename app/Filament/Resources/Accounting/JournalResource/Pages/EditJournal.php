<?php

namespace App\Filament\Resources\Accounting\JournalResource\Pages;

use App\Filament\Resources\Accounting\JournalResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditJournal extends BaseEditRecord
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

