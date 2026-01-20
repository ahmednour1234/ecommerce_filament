<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

/**
 * Report PDF Export with mPDF
 * Enhanced PDF export with Arabic RTL support
 */
class ReportPdfExport
{
    protected Collection $data;
    protected array $headers;
    protected string $title;
    protected array $metadata;
    protected bool $isRtl;
    protected string $view;
    protected ?Collection $summaryData = null;
    protected ?array $summaryHeaders = null;

    public function __construct(
        Collection $data,
        array $headers,
        string $title = 'Report',
        array $metadata = [],
        bool $isRtl = false,
        string $view = 'reports.pdf'
    ) {
        $this->data = $data;
        $this->headers = $headers;
        $this->title = $title;
        $this->metadata = $metadata;
        $this->isRtl = $isRtl;
        $this->view = $view;
    }

    /**
     * Create mPDF instance with Arabic font support
     */
    protected function createMpdf(): Mpdf
    {
        // Get default configuration
        $defaultConfig = [];
        $defaultFontData = [];
        
        try {
            $configVars = new ConfigVariables();
            $defaultConfig = $configVars->getDefaults();
            $fontDirs = $defaultConfig['fontDir'] ?? [];
            
            $fontVars = new FontVariables();
            $defaultFontData = $fontVars->getDefaults()['fontdata'] ?? [];
        } catch (\Exception $e) {
            // Fallback if classes are not available
            $fontDirs = [];
            $defaultFontData = [];
        }

        // Merge with custom font directories
        $fontDirs = array_merge($fontDirs, [
            public_path('fonts'),
            resource_path('fonts'),
        ]);

        // Configure Arabic fonts - merge with defaults
        $fontData = array_merge($defaultFontData, [
            'dejavusans' => [
                'R' => 'DejaVuSans.ttf',
                'B' => 'DejaVuSans-Bold.ttf',
                'I' => 'DejaVuSans-Oblique.ttf',
                'BI' => 'DejaVuSans-BoldOblique.ttf',
            ],
        ]);

        // Add custom Arabic fonts if they exist
        if (file_exists(public_path('fonts/Tajawal-Regular.ttf'))) {
            $fontData['tajawal'] = [
                'R' => 'Tajawal-Regular.ttf',
                'B' => 'Tajawal-Bold.ttf',
            ];
        }

        if (file_exists(public_path('fonts/Cairo-Regular.ttf'))) {
            $fontData['cairo'] = [
                'R' => 'Cairo-Regular.ttf',
                'B' => 'Cairo-Bold.ttf',
            ];
        }

        $config = [
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'orientation' => 'L',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'default_font' => 'dejavusans',
            'direction' => $this->isRtl ? 'rtl' : 'ltr',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ];

        return new Mpdf($config);
    }

    /**
     * Render view to HTML
     */
    protected function renderView(): string
    {
        // Ensure all data is properly UTF-8 encoded
        $cleanData = $this->data->map(function($row) {
            if (is_array($row)) {
                return array_map(function($value) {
                    return $this->ensureUtf8($value);
                }, array_values($row));
            }
            $array = (array) $row;
            return array_map(function($value) {
                return $this->ensureUtf8($value);
            }, array_values($array));
        })->toArray();

        $cleanHeaders = array_map(function($header) {
            return $this->ensureUtf8($header);
        }, $this->headers);

        $cleanTitle = $this->ensureUtf8($this->title);
        
        $cleanMetadata = array_map(function($value) {
            return $this->ensureUtf8($value);
        }, $this->metadata);

        $cleanSummaryData = null;
        $cleanSummaryHeaders = null;

        if ($this->summaryData && $this->summaryHeaders) {
            $cleanSummaryData = $this->summaryData->map(function($row) {
                if (is_array($row)) {
                    return array_map(function($value) {
                        return $this->ensureUtf8($value);
                    }, array_values($row));
                }
                $array = (array) $row;
                return array_map(function($value) {
                    return $this->ensureUtf8($value);
                }, array_values($array));
            })->toArray();

            $cleanSummaryHeaders = array_map(function($header) {
                return $this->ensureUtf8($header);
            }, $this->summaryHeaders);
        }

        $html = view($this->view, [
            'title' => $cleanTitle,
            'headers' => $cleanHeaders,
            'rows' => $cleanData,
            'metadata' => $cleanMetadata,
            'isRtl' => $this->isRtl,
            'summaryRows' => $cleanSummaryData,
            'summaryHeaders' => $cleanSummaryHeaders,
        ])->render();

        // Ensure the HTML is UTF-8
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        }

        return $html;
    }

    /**
     * Ensure value is properly UTF-8 encoded
     */
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

        // Try to convert to UTF-8
        $converted = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1256', 'ASCII'], true));
        
        // If conversion fails, remove invalid characters
        if ($converted === false || !mb_check_encoding($converted, 'UTF-8')) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return $converted;
    }

    /**
     * Download PDF
     */
    public function download(string $filename = 'export.pdf'): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $mpdf = $this->createMpdf();
        $html = $this->renderView();
        
        $mpdf->WriteHTML($html);
        
        return response()->streamDownload(function () use ($mpdf, $filename) {
            $mpdf->Output($filename, 'D');
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Stream PDF
     */
    public function stream(string $filename = 'export.pdf'): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $mpdf = $this->createMpdf();
        $html = $this->renderView();
        
        $mpdf->WriteHTML($html);
        
        return response()->streamDownload(function () use ($mpdf) {
            $mpdf->Output('', 'I');
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Get PDF as string
     */
    public function output(): string
    {
        $mpdf = $this->createMpdf();
        $html = $this->renderView();
        
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('', 'S');
    }

    public function setSummaryData(Collection $summaryData, array $summaryHeaders): void
    {
        $this->summaryData = $summaryData;
        $this->summaryHeaders = $summaryHeaders;
    }
}

