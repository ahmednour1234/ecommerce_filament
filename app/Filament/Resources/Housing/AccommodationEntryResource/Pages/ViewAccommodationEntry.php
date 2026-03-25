<?php

namespace App\Filament\Resources\Housing\AccommodationEntryResource\Pages;

use App\Filament\Resources\Housing\AccommodationEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAccommodationEntry extends ViewRecord
{
    protected static string $resource = AccommodationEntryResource::class;

    public function getTitle(): string
    {
        return 'تفاصيل إدخال الإيواء';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('تعديل'),
        ];
    }
}
