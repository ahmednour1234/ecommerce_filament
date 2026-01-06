<?php

namespace App\Filament\Resources\HR\HolidayResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\HolidayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHolidays extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.add_new_holiday', [], null, 'dashboard') ?: 'Add New Holiday'),
            Actions\Action::make('show_calendar')
                ->label(tr('actions.show_calendar', [], null, 'dashboard') ?: 'Show Calendar')
                ->icon('heroicon-o-calendar-days')
                ->url(fn () => \App\Filament\Pages\HR\HolidaysCalendarPage::getUrl())
                ->visible(fn () => auth()->user()?->can('hr_holidays.calendar') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf()),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }
}

