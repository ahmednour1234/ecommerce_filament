<?php

namespace App\Filament\Resources\HR\LeaveRequestResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\LeaveRequestResource;
use App\Services\HR\LeaveRequestService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Contracts\View\View;

class ListLeaveRequests extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.add', [], null, 'dashboard') ?: 'Add'),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => auth()->user()?->can('hr.leave_requests.export') ?? false)
                ->action(fn () => $this->exportToExcel()),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => auth()->user()?->can('hr.leave_requests.export') ?? false)
                ->action(fn () => $this->exportToPdf()),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->visible(fn () => auth()->user()?->can('hr.leave_requests.export') ?? false)
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        $service = app(LeaveRequestService::class);
        $stats = $service->getStatistics();

        return [
            LeaveRequestResource\Widgets\LeaveRequestStatsWidget::make([
                'total' => $stats['total'],
                'pending' => $stats['pending'],
                'approved' => $stats['approved'],
                'rejected' => $stats['rejected'],
            ]),
        ];
    }
}

