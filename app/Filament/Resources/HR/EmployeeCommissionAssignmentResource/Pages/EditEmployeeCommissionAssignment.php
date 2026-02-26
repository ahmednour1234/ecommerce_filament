<?php

namespace App\Filament\Resources\HR\EmployeeCommissionAssignmentResource\Pages;

use App\Filament\Resources\HR\EmployeeCommissionAssignmentResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditEmployeeCommissionAssignment extends BaseEditRecord
{
    protected static string $resource = EmployeeCommissionAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
        ];
    }
}
