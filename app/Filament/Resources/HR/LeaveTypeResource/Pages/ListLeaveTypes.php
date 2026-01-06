<?php

namespace App\Filament\Resources\HR\LeaveTypeResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveTypes extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.add', [], null, 'dashboard') ?: 'Add'),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => auth()->user()?->can('hr.leave_types.export') ?? false)
                ->action(fn () => $this->exportToExcel()),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => auth()->user()?->can('hr.leave_types.export') ?? false)
                ->action(fn () => $this->exportToPdf()),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->visible(fn () => auth()->user()?->can('hr.leave_types.export') ?? false)
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }
}

