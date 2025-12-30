<?php

namespace App\Filament\Resources\Accounting\JournalEntryResource\Pages;

use App\Filament\Resources\Accounting\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(trans_dash('accounting.print', 'Print'))
                ->icon('heroicon-o-printer')
                ->url(fn () => route('filament.admin.resources.accounting.journal-entries.print', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('export_pdf')
                ->label(trans_dash('accounting.export_pdf', 'Export PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Will be implemented in Phase 6
                }),

            Actions\Action::make('export_excel')
                ->label(trans_dash('accounting.export_excel', 'Export Excel'))
                ->icon('heroicon-o-table-cells')
                ->action(function () {
                    // Will be implemented in Phase 6
                }),
        ];
    }
}

