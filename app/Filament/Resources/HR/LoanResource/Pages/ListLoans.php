<?php

namespace App\Filament\Resources\HR\LoanResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\LoanResource;
use App\Filament\Resources\HR\LoanResource\Widgets;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoans extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->can('hr.loans.export') ?? false),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf())
                ->visible(fn () => auth()->user()?->can('hr.loans.export') ?? false),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('hr.loans.export') ?? false),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\LoanStatsWidget::class,
        ];
    }
}
