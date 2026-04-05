<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Imports\BranchTransactionImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListBranchTransactions extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = BranchTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.create_transactions') ?? false),

            Actions\Action::make('import')
                ->label(tr('actions.import_excel', [], null, 'dashboard') ?: 'استيراد اكسل')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label(tr('forms.excel_file', [], null, 'dashboard') ?: 'ملف اكسل')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('public')
                        ->directory('imports'),
                ])
                ->action(function (array $data) {
                    $this->importBranchTransactions($data['file']);
                })
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.create_transactions') ?? false),

            Actions\Action::make('download_template')
                ->label(tr('actions.download_template', [], null, 'dashboard') ?: 'تحميل نموذج')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return $this->downloadTemplate();
                })
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.create_transactions') ?? false),

            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf()),

            Actions\Action::make('print_pdf')
                ->label(tr('actions.print_pdf', [], null, 'dashboard') ?: 'Print PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $response = $this->exportToPdf();
                    $response->headers->set('Content-Disposition', 'inline; filename="' . $this->getExportFilename('pdf') . '"');
                    return $response;
                })
                ->color('gray'),
        ];
    }

    protected function downloadTemplate()
    {
        $headers = [
            'branch_id' => 'معرف الفرع / Branch ID',
            'trx_date' => 'تاريخ العملية / Transaction Date (Y-m-d)',
            'country_id' => 'معرف الدولة / Country ID',
            'currency_id' => 'معرف العملة / Currency ID',
            'finance_type_id' => 'معرف نوع التمويل / Finance Type ID',
            'amount' => 'المبلغ / Amount',
            'payment_method' => 'طريقة الدفع / Payment Method',
            'recipient_name' => 'اسم المستقبل / Recipient Name',
            'reference_no' => 'رقم المرجع / Reference No',
            'notes' => 'ملاحظات / Notes',
        ];

        $export = new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(protected array $headers) {}
            public function array(): array { return []; }
            public function headings(): array { return array_values($this->headers); }
        };

        $fileName = 'branch_transactions_template_' . date('Y-m-d_His') . '.xlsx';
        $path = 'templates/' . $fileName;

        Excel::store($export, $path, 'public');

        return Storage::disk('public')->download($path, $fileName);
    }

    protected function importBranchTransactions(string $filePath)
    {
        try {
            $import = new BranchTransactionImport();
            Excel::import($import, $filePath, 'public');

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            $message = tr('finance.transactions.import_success', [], null, 'dashboard') ?: 'تم استيراد ' . $successCount . ' عملية بنجاح';

            if (!empty($errors)) {
                $errorMessage = 'حدثت أخطاء في الصفوف التالية: ';
                foreach (array_slice($errors, 0, 5) as $error) {
                    $errorMessage .= 'الصف ' . $error['row'] . ': ' . $error['error'] . '; ';
                }
                if (count($errors) > 5) {
                    $errorMessage .= 'و ' . (count($errors) - 5) . ' أخطاء أخرى';
                }

                Notification::make()
                    ->warning()
                    ->title('استيراد جزئي')
                    ->body($message . ' ' . $errorMessage)
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title('تم الاستيراد بنجاح')
                    ->body($message)
                    ->send();
            }

            $this->redirect($this->getUrl());
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('خطأ في الاستيراد')
                ->body($e->getMessage())
                ->send();
        }
    }
}
