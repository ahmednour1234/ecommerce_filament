<?php

namespace App\Filament\Resources\Rental\RentalContractResource\Pages;

use App\Filament\Resources\Rental\RentalContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRentalContract extends ViewRecord
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print_contract')
                ->label(tr('rental.print.contract', [], null, 'dashboard') ?: 'Print Contract')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('rental.contracts.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('rental.print.contract')),
            Actions\Action::make('print_invoice')
                ->label(tr('rental.print.invoice', [], null, 'dashboard') ?: 'Print Invoice')
                ->icon('heroicon-o-document-text')
                ->url(fn () => route('rental.contracts.invoice', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('rental.print.invoice')),
        ];
    }
}
