<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class TableExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $data;
    protected array $headers;
    protected string $title;

    public function __construct(Collection $data, array $headers, string $title = 'Export')
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->title = $title;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function map($row): array
    {
        if (is_array($row)) {
            return array_values($row);
        }

        if (is_object($row)) {
            return array_values((array) $row);
        }

        return [];
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
        return $this->title;
    }
}

