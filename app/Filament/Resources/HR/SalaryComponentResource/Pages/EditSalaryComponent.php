<?php

namespace App\Filament\Resources\HR\SalaryComponentResource\Pages;

use App\Filament\Resources\HR\SalaryComponentResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditSalaryComponent extends BaseEditRecord
{
    protected static string $resource = SalaryComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_components.delete') ?? false),
        ];
    }
}
