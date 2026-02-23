<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Filament\Widgets\Recruitment\RecruitmentContractStatsWidget;
use App\Imports\RecruitmentContractsImport;
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

        if (str_starts_with($path, '/public/')) {
            $path = substr($path, 7);
        }

        if (str_contains($path, '/admin/') && !str_contains($path, '/admin/public/')) {
            $newPath = str_replace('/admin/', '/admin/public/', $path);

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
        return [
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
            'passport_no' => 'passport_no / رقم الجواز',
            'client_name' => 'client_name / اسم العميل',
            'sponsor_name' => 'sponsor_name / اسم الكفيل',
            'branch_name' => 'branch_name / اسم الفرع',
            'visa_no' => 'visa_no / رقم التأشيرة',
            'id_number' => 'ID number / رقم الهوية',
            'note' => 'note / ملاحظات',
            'arrival_date' => 'arrival_date (YYYY-MM-DD) / تاريخ الوصول',
            'issue_date' => 'issue_date (YYYY-MM-DD) / تاريخ الإصدار',
            'status_code' => 'status_code (1-14) / رمز الحالة',
            'name_of_the_airport' => 'NAME OF THE AIRPORT / اسم المطار',
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

        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn () => view('filament.components.recruitment-contracts-search-style')
        );
    }
}
