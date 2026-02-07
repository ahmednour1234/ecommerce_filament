<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Support\Arrayable;
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
        try {
            $viewData = $this->getViewData();

            $testJson = json_encode($viewData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            if ($testJson === false) {
                throw new \RuntimeException('View data contains invalid UTF-8 after sanitization');
            }

            $isRtl = $this->detectRtl($viewData);

            $pdf = Pdf::loadView('exports.table-pdf', $viewData)
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('enable-local-file-access', true)
                ->setOption('defaultFont', $isRtl ? 'Cairo' : 'DejaVu Sans')
                ->setOption('fontDir', [
                    public_path('fonts'),
                    resource_path('fonts'),
                    storage_path('fonts'),
                ])
                ->setOption('fontCache', storage_path('fonts'));

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            $safeMessage = $this->toCleanString($e->getMessage());
            throw new \RuntimeException('PDF generation failed: ' . $safeMessage, $e->getCode(), $e);
        }
    }

    public function stream(string $filename = 'export.pdf'): \Illuminate\Http\Response
    {
        try {
            $viewData = $this->getViewData();

            $testJson = json_encode($viewData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            if ($testJson === false) {
                throw new \RuntimeException('View data contains invalid UTF-8 after sanitization');
            }

            $isRtl = $this->detectRtl($viewData);

            $pdf = Pdf::loadView('exports.table-pdf', $viewData)
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('enable-local-file-access', true)
                ->setOption('defaultFont', $isRtl ? 'Cairo' : 'DejaVu Sans')
                ->setOption('fontDir', [
                    public_path('fonts'),
                    resource_path('fonts'),
                    storage_path('fonts'),
                ])
                ->setOption('fontCache', storage_path('fonts'));

            return $pdf->stream($filename);
        } catch (\Throwable $e) {
            $safeMessage = $this->toCleanString($e->getMessage());
            throw new \RuntimeException('PDF generation failed: ' . $safeMessage, $e->getCode(), $e);
        }
    }

    /**
     * IMPORTANT:
     * - كل شيء هنا بيتنضّف deep recursion عشان مايبقاش فيه ولا byte غير UTF-8.
     * - نفس الداتا لو رجعتها JSON في أي مكان مش هتعمل Malformed UTF-8.
     */
    protected function getViewData(): array
    {
        $cleanHeaders = array_values(array_map(fn ($h) => $this->preserveUtf8($h), $this->headers));
        $cleanTitle = $this->preserveUtf8($this->title);

        $cleanRows = $this->data->map(function ($row) use ($cleanHeaders) {
            $rowArray = $this->toArraySafe($row);

            if (!is_array($rowArray)) {
                return [$this->preserveUtf8($rowArray)];
            }

            $cells = [];

            if (array_keys($rowArray) !== range(0, count($rowArray) - 1)) {
                foreach ($cleanHeaders as $header) {
                    $cells[] = $this->preserveUtf8($rowArray[$header] ?? '');
                }
            } else {
                foreach ($rowArray as $cell) {
                    if (is_array($cell) || is_object($cell)) {
                        $cell = $this->stringifyComplex($cell);
                    }
                    $cells[] = $this->preserveUtf8($cell);
                }
            }

            return $cells;
        })->toArray();

        $cleanMetadata = $this->sanitizeDeep($this->metadata);

        return [
            'title' => $cleanTitle,
            'headers' => $cleanHeaders,
            'rows' => $cleanRows,
            'metadata' => $cleanMetadata,
        ];
    }

    protected function preserveUtf8($value): string
    {
        if (is_null($value)) return '';
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_numeric($value)) return (string) $value;

        if (!is_string($value)) {
            $value = (string) $value;
        }

        if (empty($value)) return '';

        if (!mb_check_encoding($value, 'UTF-8')) {
            $detected = @mb_detect_encoding($value, ['UTF-8', 'Windows-1256', 'ISO-8859-6', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
            if ($detected && $detected !== 'UTF-8') {
                $converted = @mb_convert_encoding($value, 'UTF-8', $detected);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }

            if (function_exists('iconv')) {
                $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
                if ($iconv !== false) {
                    return $iconv;
                }
            }
        }

        return $value;
    }

    /**
     * تنظيف recursive لأي structure (array/object/collection/scalar)
     * ويرجع نفس النوع قدر الإمكان بس strings نظيفة UTF-8.
     */
    protected function sanitizeDeep($value)
    {
        if ($value instanceof Collection) {
            return $value->map(fn ($v) => $this->sanitizeDeep($v))->all();
        }

        if ($value instanceof Arrayable) {
            return $this->sanitizeDeep($value->toArray());
        }

        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                // المفاتيح كمان لازم تبقى UTF-8 لو strings
                $cleanKey = is_string($k) ? $this->toCleanString($k) : $k;
                $out[$cleanKey] = $this->sanitizeDeep($v);
            }
            return $out;
        }

        if (is_object($value)) {
            // لو object قابل للتحويل لarray
            return $this->sanitizeDeep($this->toArraySafe($value));
        }

        // scalar
        if (is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)) {
            return $this->toCleanString($value);
        }

        // أي نوع غريب
        return $this->toCleanString((string) $value);
    }

    protected function toArraySafe($row)
    {
        if ($row instanceof Collection) {
            return $row->all();
        }

        if ($row instanceof Arrayable) {
            return $row->toArray();
        }

        if (is_array($row)) {
            return $row;
        }

        if (is_object($row)) {
            // حاول تاخد public props + attributes
            try {
                return (array) $row;
            } catch (\Throwable $e) {
                return ['value' => (string) $row];
            }
        }

        return $row; // scalar
    }

    /**
     * يحوّل array/object لنص آمن بدل "Array" أو UTF-8 غلط
     */
    protected function stringifyComplex($value): string
    {
        try {
            // JSON_INVALID_UTF8_SUBSTITUTE يحط بديل للبايتات الغلط بدل ما يرمي Exception
            $json = json_encode(
                $this->sanitizeDeep($value),
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            );

            if ($json !== false) {
                return $json;
            }
        } catch (\Throwable $e) {
            // تجاهل
        }

        return '[complex]';
    }

    /**
     * ✅ أهم جزء: تحويل أي قيمة لstring نظيف UTF-8
     */
    protected function toCleanString($value): string
    {
        if (is_null($value)) return '';
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_numeric($value)) return (string) $value;

        if (!is_string($value)) {
            // لو array/object هنا يبقى خطأ استخدام: حوّله لنص
            if (is_array($value) || is_object($value)) {
                $value = $this->stringifyComplex($value);
            } else {
                $value = (string) $value;
            }
        }

        $value = trim($value);
        if ($value === '') return '';

        // شيل control chars
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $value) ?? '';

        // لو UTF-8 تمام رجّعه
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        // جرّب detect + convert (خصوصًا عربي Windows-1256)
        $detected = @mb_detect_encoding($value, ['UTF-8', 'Windows-1256', 'ISO-8859-6', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($detected && $detected !== 'UTF-8') {
            $converted = @mb_convert_encoding($value, 'UTF-8', $detected);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        // iconv تنظيف نهائي
        if (function_exists('iconv')) {
            $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($iconv !== false) {
                $value = $iconv;
            }
        }

        // آخر خطوة: بدل بايتات غير صالحة بدل ما تبوّظ JSON
        try {
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            if ($json !== false) {
                $decoded = json_decode($json, true);
                if (is_string($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // fallback: شيل أي حاجة مش printable
        $value = preg_replace('/[^\x20-\x7E\x{00A0}-\x{FFFF}]/u', '', $value) ?? '';
        return $value;
    }

    protected function detectRtl(array $viewData): bool
    {
        $text = ($viewData['title'] ?? '') . ' ' . implode(' ', $viewData['headers'] ?? []);
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }
}
