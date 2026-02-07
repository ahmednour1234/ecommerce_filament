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
        $isRtl = $this->detectRtl($viewData);
        
        $pdf = Pdf::loadView('exports.table-pdf', $viewData);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('defaultFont', $isRtl ? 'Cairo' : 'DejaVu Sans');
        $pdf->setOption('fontDir', [
            public_path('fonts'),
            resource_path('fonts'),
            storage_path('fonts'),
        ]);
        $pdf->setOption('fontCache', storage_path('fonts'));
        
        return $pdf->download($filename);
    }

    public function stream(string $filename = 'export.pdf'): \Illuminate\Http\Response
    {
        $viewData = $this->getViewData();
        $isRtl = $this->detectRtl($viewData);
        
        $pdf = Pdf::loadView('exports.table-pdf', $viewData);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('defaultFont', $isRtl ? 'Cairo' : 'DejaVu Sans');
        $pdf->setOption('fontDir', [
            public_path('fonts'),
            resource_path('fonts'),
            storage_path('fonts'),
        ]);
        $pdf->setOption('fontCache', storage_path('fonts'));
        
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

        if (empty($value)) {
            return '';
        }

        // First, try to remove invalid UTF-8 bytes
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $value);

        // Check if already valid UTF-8
        if (mb_check_encoding($value, 'UTF-8')) {
            // Validate it can be JSON encoded
            $test = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        // Try to detect and convert encoding
        $detected = @mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1256', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = @mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                $test = json_encode($converted, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
                if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                    return $converted;
                }
            }
        }

        // Remove invalid UTF-8 characters using iconv
        if (function_exists('iconv')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $value);
            if ($cleaned !== false && mb_check_encoding($cleaned, 'UTF-8')) {
                $test = json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
                if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                    return $cleaned;
                }
            }
        }

        // Fallback: use mb_convert_encoding
        $cleaned = @mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        if ($cleaned !== false && mb_check_encoding($cleaned, 'UTF-8')) {
            $test = json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            if ($test !== false && json_last_error() === JSON_ERROR_NONE) {
                return $cleaned;
            }
        }

        // Last resort: filter through json_encode/decode
        $json = @json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
        if ($json !== false) {
            $decoded = json_decode($json, true);
            if ($decoded !== null && is_string($decoded)) {
                return $decoded;
            }
        }

        // Final fallback: remove all non-printable characters
        $cleaned = preg_replace('/[^\x20-\x7E\x{00A0}-\x{FFFF}]/u', '', $value);
        return mb_convert_encoding($cleaned, 'UTF-8', 'UTF-8') ?: '';
    }

    protected function detectRtl(array $viewData): bool
    {
        $text = $viewData['title'] . implode(' ', $viewData['headers']);
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }
}

