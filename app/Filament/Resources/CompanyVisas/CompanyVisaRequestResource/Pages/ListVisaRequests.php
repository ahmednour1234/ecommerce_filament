<?php

namespace App\Filament\Resources\CompanyVisas\CompanyVisaRequestResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\CompanyVisas\CompanyVisaRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisaRequests extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = CompanyVisaRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('company_visas.actions.create_new_request', [], null, 'dashboard') ?: 'إضافة طلب تأشيرة جديد'),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'تصدير Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'تصدير PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf()),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }
}
