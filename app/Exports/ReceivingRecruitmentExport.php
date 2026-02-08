<?php

namespace App\Exports;

use App\Services\Recruitment\ReceivingRecruitmentReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class ReceivingRecruitmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $contracts;
    protected ReceivingRecruitmentReportService $service;

    public function __construct(Collection $contracts)
    {
        $this->contracts = $contracts;
        $this->service = app(ReceivingRecruitmentReportService::class);
    }

    public function collection(): Collection
    {
        return $this->contracts;
    }

    public function headings(): array
    {
        return $this->service->getExportHeaders();
    }

    public function map($contract): array
    {
        $formatted = $this->service->formatContractForExport($contract);
        
        return [
            $formatted['id'],
            $formatted['contract_no'],
            $formatted['client'],
            $formatted['worker'],
            $formatted['passport_number'],
            $formatted['arrival_date'],
            $formatted['trial_end_date'],
            $formatted['contract_end_date'],
            $formatted['status'],
            $formatted['employee'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return tr('recruitment.receiving_labor.title', [], null, 'dashboard') ?: 'استلام العمالة';
    }
}
