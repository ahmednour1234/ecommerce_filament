<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class FinanceImportErrorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $errors;

    public function __construct(array $errors)
    {
        $this->errors = collect($errors);
    }

    public function collection(): Collection
    {
        return $this->errors;
    }

    public function headings(): array
    {
        return [
            tr('pages.finance.import.error_report.row_number', [], null, 'dashboard') ?: 'Row Number',
            tr('pages.finance.import.error_report.reference_no', [], null, 'dashboard') ?: 'Reference No',
            tr('pages.finance.import.error_report.error_message', [], null, 'dashboard') ?: 'Error Message',
            tr('pages.finance.import.error_report.raw_values', [], null, 'dashboard') ?: 'Raw Values',
        ];
    }

    public function map($error): array
    {
        return [
            $error['row'] ?? '',
            $error['reference_no'] ?? '',
            $error['error'] ?? '',
            json_encode($error['values'] ?? [], JSON_UNESCAPED_UNICODE),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return tr('pages.finance.import.error_report.title', [], null, 'dashboard') ?: 'Import Errors';
    }
}
