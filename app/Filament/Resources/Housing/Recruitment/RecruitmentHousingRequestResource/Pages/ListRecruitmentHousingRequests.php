<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecruitmentHousingRequests extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = RecruitmentHousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('print')
                ->label(tr('housing.actions.print', [], null, 'dashboard') ?: 'طباعة')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
            Actions\Action::make('excel')
                ->label(tr('housing.actions.excel', [], null, 'dashboard') ?: 'إكسل')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => $this->exportToExcel()),
            Actions\Action::make('copy')
                ->label(tr('housing.actions.copy', [], null, 'dashboard') ?: 'نسخ')
                ->icon('heroicon-o-clipboard')
                ->color('info')
                ->action(function () {
                    $this->dispatch('copy-table-data');
                    \Filament\Notifications\Notification::make()
                        ->title(tr('messages.copied', [], null, 'dashboard') ?: 'تم النسخ')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getExportTitle(): ?string
    {
        return tr('housing.requests.pending', [], null, 'dashboard') ?: 'طلبات معلّقة';
    }
}
