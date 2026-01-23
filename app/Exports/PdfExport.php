<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class PdfExport
{
    protected Collection $data;
    protected array $headers;
    protected string $title;
    protected array $metadata;

    public function __construct(Collection $data, array $headers, string $title = 'Report', array $metadata = [])
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->title = $title;
        $this->metadata = $metadata;
    }

    public function download(string $filename = 'export.pdf'): \Illuminate\Http\Response
    {
        $viewData = $this->getViewData();
        
        $pdf = Pdf::loadView('exports.table-pdf', $viewData);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        
        return $pdf->download($filename);
    }

    public function stream(string $filename = 'export.pdf'): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('exports.table-pdf', $this->getViewData());

        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream($filename);
    }

    protected function getViewData(): array
    {
        $cleanRows = $this->data->map(function($row) {
            if (is_array($row)) {
                return array_values(array_map([$this, 'ensureUtf8'], $row));
            }
            if (is_object($row)) {
                $array = (array) $row;
                return array_values(array_map([$this, 'ensureUtf8'], $array));
            }
            return [$this->ensureUtf8($row)];
        })->toArray();

        $cleanHeaders = array_map([$this, 'ensureUtf8'], $this->headers);
        $cleanTitle = $this->ensureUtf8($this->title);
        $cleanMetadata = array_map(function($value) {
            if (is_string($value)) {
                return $this->ensureUtf8($value);
            }
            if (is_array($value)) {
                return array_map([$this, 'ensureUtf8'], $value);
            }
            return $value;
        }, $this->metadata);

        return [
            'title' => $cleanTitle,
            'headers' => $cleanHeaders,
            'rows' => $cleanRows,
            'metadata' => $cleanMetadata,
        ];
    }

    protected function ensureUtf8($value): string
    {
        if (is_null($value)) {
            return '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return (string) $value;
        }

        if (!is_string($value)) {
            $value = (string) $value;
        }

        // Check if already valid UTF-8
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        // Try to detect and convert encoding
        $detected = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1256', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                return $converted;
            }
        }

        // Remove invalid UTF-8 characters using iconv
        if (function_exists('iconv')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($cleaned !== false) {
                return $cleaned;
            }
        }

        // Fallback: use mb_convert_encoding with //IGNORE
        $cleaned = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        if (mb_check_encoding($cleaned, 'UTF-8')) {
            return $cleaned;
        }

        // Last resort: remove invalid UTF-8 bytes
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        return mb_convert_encoding($cleaned, 'UTF-8', 'UTF-8') ?: '';
    }
}

