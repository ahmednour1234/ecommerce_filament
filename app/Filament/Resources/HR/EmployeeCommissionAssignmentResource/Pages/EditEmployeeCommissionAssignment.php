<?php

namespace App\Filament\Resources\HR\EmployeeCommissionAssignmentResource\Pages;

use App\Filament\Resources\HR\EmployeeCommissionAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeCommissionAssignment extends EditRecord
{
    protected static string $resource = EmployeeCommissionAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
        ];
    }
}
