<?php

namespace App\Filament\Resources\Recruitment\AgentResource\Pages;

use App\Filament\Resources\Recruitment\AgentResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditAgent extends BaseEditRecord
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
