<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceTransfer extends ViewRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print_invoice')
                ->label('طباعة الفاتورة')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('service-transfers.invoice', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('service_transfers.print') ?? false),
        ];
    }
}
