<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditComplaint extends BaseEditRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
