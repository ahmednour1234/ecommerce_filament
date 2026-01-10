<?php

namespace App\Filament\Resources\HR\LoanTypeResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\LoanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanTypes extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = LoanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->can('hr.loan_types.export') ?? false),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf())
                ->visible(fn () => auth()->user()?->can('hr.loan_types.export') ?? false),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('hr.loan_types.export') ?? false),
        ];
    }
}
