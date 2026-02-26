<?php

namespace App\Filament\Resources\HR\CommissionTypeResource\Pages;

use App\Filament\Resources\HR\CommissionTypeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditCommissionType extends BaseEditRecord
{
    protected static string $resource = CommissionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commission_types.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commission_types.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commission_types.delete') ?? false),
        ];
    }
}
