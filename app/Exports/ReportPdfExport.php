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
        $html = view($this->view, [
            'title' => $this->title,
            'headers' => $this->headers,
            'rows' => $this->data->map(fn($row) => is_array($row) ? array_values($row) : array_values((array) $row))->toArray(),
            'metadata' => $this->metadata,
            'isRtl' => $this->isRtl,
        ])->render();

        return $html;
    }

    /**
     * Download PDF
     */
    public function download(string $filename = 'export.pdf'): \Illuminate\Http\Response
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
    public function stream(string $filename = 'export.pdf'): \Illuminate\Http\Response
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
}

