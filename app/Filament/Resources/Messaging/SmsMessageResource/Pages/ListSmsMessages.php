<?php

namespace App\Filament\Resources\Messaging\SmsMessageResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Messaging\SmsMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmsMessages extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = SmsMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label(tr('actions.excel', [], null, 'dashboard') ?: 'Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }
}
