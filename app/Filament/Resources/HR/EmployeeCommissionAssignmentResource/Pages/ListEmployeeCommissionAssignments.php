<?php

namespace App\Filament\Resources\HR\EmployeeCommissionAssignmentResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\EmployeeCommissionAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeCommissionAssignments extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = EmployeeCommissionAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.create_assignment', [], null, 'dashboard') ?: 'Create Assignment')
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.create') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.view_any') ?? false),
        ];
    }
}
