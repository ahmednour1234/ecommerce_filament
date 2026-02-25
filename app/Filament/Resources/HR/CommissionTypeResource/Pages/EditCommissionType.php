<?php

namespace App\Filament\Resources\HR\CommissionTypeResource\Pages;

use App\Filament\Resources\HR\CommissionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommissionType extends EditRecord
{
    protected static string $resource = CommissionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commission_types.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commission_types.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commission_types.delete') ?? false),
        ];
    }
}
