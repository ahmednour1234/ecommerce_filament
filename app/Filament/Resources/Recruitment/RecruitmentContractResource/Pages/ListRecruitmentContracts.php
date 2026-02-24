<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Filament\Widgets\Recruitment\RecruitmentContractStatsWidget;
use App\Imports\RecruitmentContractsImport;
use App\Jobs\Recruitment\CalculateContractAlertsJob;
use App\Services\Recruitment\ContractAlertsService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class ListRecruitmentContracts extends ListRecords
{
    protected static string $resource = RecruitmentContractResource::class;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        $url = parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
        return static::addPublicToUrl($url);
    }

    protected static function addPublicToUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        if (str_contains($path, '/admin/') && !str_contains($path, '/public/admin/')) {
            if (str_starts_with($path, '/public/')) {
                $path = substr($path, 7);
            }
            $newPath = str_replace('/admin/', '/public/admin/', $path);

            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

            return $scheme . '://' . $host . $newPath . $query . $fragment;
        }

        return $url;
    }

    protected function getHeaderActions(): array
    {
        $alertsService = app(ContractAlertsService::class);
        $alertsCount = $alertsService->getAlertsCount();

        return [
            Actions\Action::make('alerts')
                ->label(tr('recruitment_contract.alerts.title', [], null, 'dashboard') ?: 'تنبيهات العقود')
                ->icon('heroicon-o-bell')
                ->color($alertsCount > 0 ? 'warning' : 'gray')
                ->badge($alertsCount > 0 ? $alertsCount : null)
                ->badgeColor('danger')
                ->url(\App\Filament\Pages\Recruitment\ContractAlertsPage::getUrl()),
            Actions\CreateAction::make()
                ->label(tr('recruitment_contract.actions.create', [], null, 'dashboard') ?: 'Create Contract'),
            Actions\Action::make('download_template')
                ->label(tr('recruitment_contract.actions.download_template', [], null, 'dashboard') ?: 'Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->downloadTemplate();
                }),
            Actions\Action::make('import')
                ->label(tr('recruitment_contract.actions.import', [], null, 'dashboard') ?: 'Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label(tr('recruitment_contract.actions.excel_file', [], null, 'dashboard') ?: 'Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('public')
                        ->directory('imports'),
                ])
                ->action(function (array $data) {
                    $this->importContracts($data['file']);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RecruitmentContractStatsWidget::class,
        ];
    }

    protected function downloadTemplate()
    {
        $headers = [
            'name_of_the_worker' => 'NAME OF THE WORKER / اسم العامل',
            'passport_no' => 'PASSPORT NO / رقم الجواز',
            'client_name' => 'CLIENT NAME / اسم العميل',
            'client_national_id' => 'CLIENT NATIONAL ID / رقم هوية العميل',
            'sponsor_name' => 'SPONSOR NAME / اسم الكفيل',
            'branch_name' => 'BRANCH NAME / اسم الفرع',
            'visa_no' => 'VISA NO / رقم التأشيرة',
            'visa_type' => 'VISA TYPE (paid/domestic_labor/comprehensive_qualification) / نوع التأشيرة',
            'visa_date' => 'VISA DATE (YYYY-MM-DD) / تاريخ التأشيرة',
            'arrival_country' => 'ARRIVAL COUNTRY / محطة الوصول',
            'departure_country' => 'DEPARTURE COUNTRY / محطة القدوم',
            'receiving_station' => 'RECEIVING STATION / محطة الاستلام',
            'profession' => 'PROFESSION / المهنة',
            'nationality' => 'NATIONALITY / الجنسية',
            'gender' => 'GENDER (male/female) / الجنس',
            'experience' => 'EXPERIENCE / الخبرة',
            'religion' => 'RELIGION / الدين',
            'workplace_ar' => 'WORKPLACE AR / مكان العمل (عربي)',
            'workplace_en' => 'WORKPLACE EN / مكان العمل (إنجليزي)',
            'monthly_salary' => 'MONTHLY SALARY / الراتب الشهري',
            'gregorian_request_date' => 'GREGORIAN REQUEST DATE (YYYY-MM-DD) / تاريخ الطلب',
            'hijri_request_date' => 'HIJRI REQUEST DATE / التاريخ الهجري',
            'status' => 'STATUS (new/processing/contract_signed/ticket_booked/worker_received/closed/returned) / الحالة',
            'payment_status' => 'PAYMENT STATUS (paid/partial/unpaid) / حالة الدفع',
            'musaned_contract_no' => 'MUSANED CONTRACT NO / رقم عقد مساند',
            'musaned_documentation_contract_no' => 'MUSANED DOCUMENTATION CONTRACT NO / رقم عقد توثيق مساند',
            'musaned_auth_no' => 'MUSANED AUTH NO / رقم تفويض مساند',
            'musaned_contract_date' => 'MUSANED CONTRACT DATE (YYYY-MM-DD) / تاريخ عقد مساند',
        ];

        $export = new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(protected array $headers) {}
            public function array(): array { return []; }
            public function headings(): array { return array_values($this->headers); }
        };

        $fileName = 'recruitment_contracts_template_' . date('Y-m-d_His') . '.xlsx';
        $path = 'templates/' . $fileName;

        Excel::store($export, $path, 'public');

        return Storage::disk('public')->download($path, $fileName);
    }

    protected function importContracts(string $filePath)
    {
        try {
            $import = new RecruitmentContractsImport();
            Excel::import($import, $filePath, 'public');

            $successCount = $import->getSuccessCount();
            $skippedCount = $import->getSkippedCount();
            $errors = $import->getErrors();

            $message = "تم إضافة {$successCount} عقد بنجاح";
            if ($skippedCount > 0) {
                $message .= " | تم تخطي {$skippedCount} صف";
            }

            Notification::make()
                ->title('اكتمل الاستيراد')
                ->body($message)
                ->success()
                ->send();

            if (!empty($errors)) {
                $errorMessage = "عدد الأخطاء: " . count($errors) . "\n\n";
                $errorMessage .= implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\n\n... و " . (count($errors) - 10) . " خطأ آخر";
                }

                Notification::make()
                    ->title('أخطاء الاستيراد')
                    ->body($errorMessage)
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('فشل الاستيراد')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function mount(): void
    {
        parent::mount();

        CalculateContractAlertsJob::dispatch();

        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn () => view('filament.components.recruitment-contracts-search-style')
        );
    }
}
