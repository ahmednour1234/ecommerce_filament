<?php

namespace App\Filament\Resources\HR\SalaryComponentResource\Pages;

use App\Filament\Resources\HR\SalaryComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalaryComponent extends EditRecord
{
    protected static string $resource = SalaryComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_components.delete') ?? false),
        ];
    }
}
