<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\ClientResource;
use App\Imports\ClientsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListClients extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('general.clients.add_client', [], null, 'dashboard') ?: 'Add Client'),
            Actions\Action::make('download_template')
                ->label(tr('general.clients.download_template', [], null, 'dashboard') ?: 'Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->downloadTemplate();
                }),
            Actions\Action::make('import')
                ->label(tr('general.clients.import', [], null, 'dashboard') ?: 'Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label(tr('general.clients.excel_file', [], null, 'dashboard') ?: 'Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('public')
                        ->directory('imports'),
                ])
                ->action(function (array $data) {
                    $this->importClients($data['file']);
                }),
        ];
    }

    protected function downloadTemplate()
    {
        $headers = [
            'name_ar' => 'الاسم (عربي)',
            'name_en' => 'Name (English)',
            'national_id' => 'رقم الهوية / National ID',
            'mobile' => 'رقم الجوال / Mobile',
            'mobile2' => 'رقم الجوال 2 / Mobile 2',
            'email' => 'البريد الإلكتروني / Email',
            'birth_date' => 'تاريخ الميلاد / Birth Date',
            'marital_status' => 'الحالة الاجتماعية / Marital Status (single/married/divorced/widowed)',
            'classification' => 'التصنيف / Classification (new/vip/blocked)',
            'city_name' => 'المدينة / City',
            'district_name' => 'الحي / District',
            'street_name' => 'اسم الشارع / Street Name',
            'building_no' => 'رقم المبنى / Building No',
            'unit_no' => 'رقم الوحدة / Unit No',
            'postal_code' => 'الرمز البريدي / Postal Code',
            'housing_type' => 'نوع السكن / Housing Type (villa/building/apartment/farm)',
            'source' => 'المصدر / Source',
        ];

        $export = new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(protected array $headers) {}
            public function array(): array { return []; }
            public function headings(): array { return array_values($this->headers); }
        };

        $fileName = 'clients_template_' . date('Y-m-d_His') . '.xlsx';
        $path = 'templates/' . $fileName;
        
        Excel::store($export, $path, 'public');
        
        return Storage::disk('public')->download($path, $fileName);
    }

    protected function importClients(string $filePath)
    {
        try {
            $import = new ClientsImport();
            Excel::import($import, $filePath, 'public');
            
            $addedColumns = $import->getAddedColumns();
            $errors = $import->getErrors();
            
            $message = tr('general.clients.import_success', [], null, 'dashboard') ?: 'Clients imported successfully.';
            
            if (!empty($addedColumns)) {
                $message .= ' ' . tr('general.clients.columns_added', [], null, 'dashboard') ?: 'New columns added: ' . implode(', ', $addedColumns);
            }
            
            Notification::make()
                ->title(tr('general.clients.import_complete', [], null, 'dashboard') ?: 'Import Complete')
                ->body($message)
                ->success()
                ->send();
            
            if (!empty($errors)) {
                Notification::make()
                    ->title(tr('general.clients.import_errors', [], null, 'dashboard') ?: 'Import Errors')
                    ->body(implode("\n", array_slice($errors, 0, 5)))
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title(tr('general.clients.import_failed', [], null, 'dashboard') ?: 'Import Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
