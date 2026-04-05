<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceImportTemplateExport implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    protected string $kind;
    protected ?string $branchName;

    public function __construct(string $kind, ?string $branchName = null)
    {
        $this->kind = $kind;
        $this->branchName = $branchName;
    }

    public function array(): array
    {
        $headers = [
            'رقم السند / الفاتورة',
            'التاريخ',
            'البيان',
            $this->kind === 'expense' ? 'المصروف' : 'الإيراد',
        ];

        $data = [];

        if ($this->branchName) {
            $data[] = ['الفرع: ' . $this->branchName, '', '', ''];
        }

        $data[] = $headers;

        for ($i = 1; $i <= 100; $i++) {
            $data[] = ['', '', '', ''];
        }

        return $data;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->setRightToLeft(true);

        $headerRow = $this->branchName ? 2 : 1;
        $dataStartRow = $headerRow + 1;
        $lastRow = $headerRow + 100;

        if ($this->branchName) {
            $infoStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '1F497D'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCE6F1'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->mergeCells('A1:D1');
            $sheet->getStyle('A1:D1')->applyFromArray($infoStyle);
            $sheet->getRowDimension(1)->setRowHeight(22);
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00B050'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle("A{$headerRow}:D{$headerRow}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle("A{$headerRow}:D{$lastRow}")->applyFromArray($borderStyle);

        $sheet->freezePane("A{$dataStartRow}");

        for ($row = $dataStartRow; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 16,
            'C' => 60,
            'D' => 18,
        ];
    }

    public function title(): string
    {
        return $this->kind === 'expense' ? 'المصروفات' : 'الإيرادات';
    }
}
