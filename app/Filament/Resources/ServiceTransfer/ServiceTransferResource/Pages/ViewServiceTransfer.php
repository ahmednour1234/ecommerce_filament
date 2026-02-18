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
            Actions\Action::make('add_payment')
                ->label('إضافة دفعة')
                ->icon('heroicon-o-plus-circle')
                ->url(fn () => ServiceTransferResource::getUrl('payments', ['record' => $this->record->id]))
                ->visible(fn () => auth()->user()?->can('service_transfer.payments.create') ?? false),
            Actions\Action::make('upload_document')
                ->label('رفع وثيقة')
                ->icon('heroicon-o-document-plus')
                ->url(fn () => ServiceTransferResource::getUrl('documents', ['record' => $this->record->id]))
                ->visible(fn () => auth()->user()?->can('service_transfers.documents.upload') ?? false),
        ];
    }
}
