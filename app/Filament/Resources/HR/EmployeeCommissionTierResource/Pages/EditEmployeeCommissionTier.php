<?php

namespace App\Filament\Resources\HR\EmployeeCommissionTierResource\Pages;

use App\Filament\Resources\HR\EmployeeCommissionTierResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditEmployeeCommissionTier extends BaseEditRecord
{
    protected static string $resource = EmployeeCommissionTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
        ];
    }
}
