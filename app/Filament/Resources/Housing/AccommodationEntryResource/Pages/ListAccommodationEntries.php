<?php

namespace App\Filament\Resources\Housing\AccommodationEntryResource\Pages;

use App\Filament\Resources\Housing\AccommodationEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccommodationEntries extends ListRecords
{
    protected static string $resource = AccommodationEntryResource::class;

    public function getTitle(): string
    {
        return 'إدخالات الإيواء';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة إدخال جديد'),
        ];
    }
}
