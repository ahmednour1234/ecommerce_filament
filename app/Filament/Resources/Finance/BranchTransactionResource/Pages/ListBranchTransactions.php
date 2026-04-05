<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Pages\Finance\ImportBranchTransactionsPage;
use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
                ->label(tr('actions.import_excel', [], null, 'dashboard') ?: 'استيراد من Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->url(fn () => ImportBranchTransactionsPage::getUrl())
                ->visible(fn () => auth()->user()?->hasRole('super_admin')
                    || auth()->user()?->can('finance.transactions.import')
                    || auth()->user()?->can('finance.create_transactions')),

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
}

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
                ->label(tr('actions.import_excel', [], null, 'dashboard') ?: 'استيراد من Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->url(fn () => ImportBranchTransactionsPage::getUrl())
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.transactions.import') || auth()->user()?->can('finance.create_transactions')),
                        ->schema([
                            \Filament\Forms\Components\Select::make('branch_id')
                                ->label('الفرع')
                                ->options(function () {
                                    $user = auth()->user();
                                    if ($user?->hasRole('super_admin')) {
                                        return \App\Models\MainCore\Branch::pluck('name', 'id');
                                    }
                                    $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
                                    if (!empty($user?->branch_id)) {
                                        $userBranches[] = (int) $user->branch_id;
                                    }
                                    return \App\Models\MainCore\Branch::whereIn('id', array_unique($userBranches))->pluck('name', 'id');
                                })
                                ->searchable(),

                            \Filament\Forms\Components\Select::make('finance_type_id')
                                ->label('النوع')
                                ->options(function () {
                                    return \App\Models\Finance\FinanceType::where('is_active', true)->get()
                                        ->mapWithKeys(fn($type) => [
                                            $type->id => $type->name['ar'] ?? $type->name['en'] ?? ''
                                        ]);
                                })
                                ->searchable(),

                            \Filament\Forms\Components\Select::make('currency_id')
                                ->label('العملة')
                                ->options(function () {
                                    return \App\Models\MainCore\Currency::get()
                                        ->mapWithKeys(fn($currency) => [
                                            $currency->id => $currency->code . ' - ' . ($currency->name ?? '')
                                        ]);
                                })
                                ->searchable(),

                            \Filament\Forms\Components\Select::make('country_id')
                                ->label('الدولة')
                                ->options(function () {
                                    return \App\Models\MainCore\Country::get()
                                        ->mapWithKeys(fn($country) => [
                                            $country->id => $country->name['ar'] ?? $country->name['en'] ?? ''
                                        ]);
                                })
                                ->searchable(),

                            \Filament\Forms\Components\DatePicker::make('trx_date')
                                ->label('تاريخ العملية الافتراضي')
                                ->default(now()),

                            \Filament\Forms\Components\TextInput::make('payment_method')
                                ->label('طريقة الدفع'),

                            \Filament\Forms\Components\Textarea::make('notes')
                                ->label('ملاحظات عامة')
                                ->rows(3),

                            \Filament\Forms\Components\Toggle::make('allow_partial_import')
                                ->label('السماح بالاستيراد الجزئي')
                                ->helperText('استيراد الصفوف الصحيحة حتى لو كان هناك أخطاء')
                                ->default(true),
                        ]),

                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('ملف اكسل')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('public')
                        ->directory('imports'),
                ])
                ->action(function (array $data) {
                    $this->importBranchTransactions($data);
                }),

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

    protected function importBranchTransactions(array $formData)
    {
        try {
            $filePath = $formData['file'];
            $import = new BranchTransactionImport(
                branch_id: $formData['branch_id'] ?? null,
                finance_type_id: $formData['finance_type_id'] ?? null,
                currency_id: $formData['currency_id'] ?? null,
                country_id: $formData['country_id'] ?? null,
                default_date: $formData['trx_date'] ?? null,
                payment_method: $formData['payment_method'] ?? null,
                notes: $formData['notes'] ?? null,
                allow_partial: $formData['allow_partial_import'] ?? true,
            );
            Excel::import($import, $filePath, 'public');

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            $message = 'تم استيراد ' . $successCount . ' عملية بنجاح';

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
