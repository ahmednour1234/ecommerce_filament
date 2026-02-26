<?php

namespace App\Filament\Resources\Recruitment\LaborerResource\Pages;

use App\Filament\Resources\Recruitment\LaborerResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditLaborer extends BaseEditRecord
{
    protected static string $resource = LaborerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(tr('general.actions.details', [], null, 'dashboard') ?: 'Details'),
            DeleteAction::make()
                ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete'),
        ];
    }
}
