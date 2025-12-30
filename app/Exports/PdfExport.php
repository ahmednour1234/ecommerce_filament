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
        $pdf = Pdf::loadView('exports.table-pdf', [
            'title' => $this->title,
            'headers' => $this->headers,
            'rows' => $this->data->map(fn($row) => is_array($row) ? array_values($row) : array_values((array) $row))->toArray(),
            'metadata' => $this->metadata,
        ]);

        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download($filename);
    }

    public function stream(string $filename = 'export.pdf'): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('exports.table-pdf', [
            'title' => $this->title,
            'headers' => $this->headers,
            'rows' => $this->data->map(fn($row) => is_array($row) ? array_values($row) : array_values((array) $row))->toArray(),
            'metadata' => $this->metadata,
        ]);

        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream($filename);
    }
}

