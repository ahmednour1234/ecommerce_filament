<?php

namespace App\Filament\Resources\Messaging\MessageContactResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Messaging\MessageContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = MessageContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.add', [], null, 'dashboard') ?: 'إضافة'),
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
