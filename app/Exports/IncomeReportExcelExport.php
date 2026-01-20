<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class IncomeReportExcelExport implements WithMultipleSheets
{
    protected Collection $detailedData;
    protected Collection $summaryData;
    protected string $title;

    public function __construct(Collection $detailedData, Collection $summaryData, string $title = 'Income Report')
    {
        $this->detailedData = $detailedData;
        $this->summaryData = $summaryData;
        $this->title = $title;
    }

    public function sheets(): array
    {
        return [
            new IncomeReportDetailedSheet($this->detailedData),
            new IncomeReportSummarySheet($this->summaryData),
        ];
    }
}

class IncomeReportDetailedSheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Branch',
            'Country',
            'Currency',
            'Category',
            'Amount',
            'Payment Method',
            'Reference No',
            'Payer',
            'Created By',
        ];
    }

    public function map($row): array
    {
        if (is_array($row)) {
            return array_values($row);
        }
        return array_values((array) $row);
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
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
        return 'Detailed Transactions';
    }
}

class IncomeReportSummarySheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Category',
            'Transactions Count',
            'Total Amount',
        ];
    }

    public function map($row): array
    {
        if (is_array($row)) {
            return array_values($row);
        }
        return array_values((array) $row);
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Summary by Category';
    }
}
