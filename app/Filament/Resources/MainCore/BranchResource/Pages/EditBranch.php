<?php

namespace App\Filament\Resources\MainCore\BranchResource\Pages;

use App\Filament\Resources\MainCore\BranchResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditBranch extends BaseEditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

