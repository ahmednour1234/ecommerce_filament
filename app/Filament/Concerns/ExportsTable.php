<?php

namespace App\Filament\Concerns;

use App\Exports\PdfExport;
use App\Exports\TableExport;
use App\Services\Export\TableExportService;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait ExportsTable
{
    protected function getExportService(): TableExportService
    {
        return app(TableExportService::class);
    }

    /**
     * Get table data for export
     */
    protected function getTableDataForExport(Table $table): array
    {
        $tableQuery = $table->getQuery();
        $columns = $this->extractTableColumns($table);
        
        // Get all records (no pagination for export)
        $records = $tableQuery->get();
        
        // Format records
        $formattedData = $records->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $column) {
                $key = $column['name'];
                $label = $column['label'];
                $value = $this->getColumnValue($record, $key, $column);
                $row[$label] = $value;
            }
            return $row;
        });

        $headers = array_column($columns, 'label');
        
        return [
            'data' => $formattedData,
            'headers' => $headers,
        ];
    }

    /**
     * Extract column definitions from table
     */
    protected function extractTableColumns(Table $table): array
    {
        $columns = [];
        $tableColumns = $table->getColumns();
        
        foreach ($tableColumns as $column) {
            // Skip hidden columns
            if (method_exists($column, 'isHidden') && $column->isHidden()) {
                continue;
            }
            
            $name = $column->getName();
            $label = $column->getLabel() ?? $name;
            
            $columns[] = [
                'name' => $name,
                'label' => $label,
                'type' => get_class($column),
            ];
        }
        
        return $columns;
    }

    /**
     * Get column value from record
     */
    protected function getColumnValue($record, string $key, array $column): mixed
    {
        // Handle nested relationships (e.g., 'customer.name')
        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            $value = $record;
            foreach ($parts as $part) {
                if (is_object($value) && isset($value->$part)) {
                    $value = $value->$part;
                } elseif (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return '';
                }
            }
            return $value ?? '';
        }

        // Handle direct attributes
        if (is_object($record)) {
            $value = $record->$key ?? $record->getAttribute($key) ?? '';
            
            // Format based on type
            if (is_numeric($value) && str_contains($key, 'total') || str_contains($key, 'amount') || str_contains($key, 'price')) {
                return number_format((float) $value, 2);
            }
            
            if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                return $value->format('Y-m-d');
            }
            
            return $value;
        }

        return $record[$key] ?? '';
    }

    /**
     * Export table to Excel
     */
    public function exportToExcel(?Table $table = null, string $filename = null): BinaryFileResponse
    {
        $table = $table ?? $this->table($this->makeTable());
        $exportData = $this->getTableDataForExport($table);
        $title = $this->getExportTitle() ?? 'Export';
        $filename = $filename ?? $this->getExportFilename('xlsx');
        
        $export = new TableExport($exportData['data'], $exportData['headers'], $title);
        
        return Excel::download($export, $filename);
    }

    /**
     * Export table to PDF
     */
    public function exportToPdf(?Table $table = null, string $filename = null): \Illuminate\Http\Response
    {
        $table = $table ?? $this->table($this->makeTable());
        $exportData = $this->getTableDataForExport($table);
        $title = $this->getExportTitle() ?? 'Report';
        $filename = $filename ?? $this->getExportFilename('pdf');
        $metadata = $this->getExportMetadata();
        
        $export = new PdfExport($exportData['data'], $exportData['headers'], $title, $metadata);
        
        return $export->download($filename);
    }

    /**
     * Get print view URL
     */
    public function getPrintUrl(?Table $table = null): string
    {
        $table = $table ?? $this->table($this->makeTable());
        $exportData = $this->getTableDataForExport($table);
        $title = $this->getExportTitle() ?? 'Report';
        $metadata = $this->getExportMetadata();
        
        // Store data in session for print view
        session()->flash('print_data', [
            'title' => $title,
            'headers' => $exportData['headers'],
            'rows' => $exportData['data']->map(fn($row) => array_values($row))->toArray(),
            'metadata' => $metadata,
        ]);
        
        return route('filament.exports.print');
    }

    /**
     * Get export title (override in implementing class)
     */
    protected function getExportTitle(): ?string
    {
        return null;
    }

    /**
     * Get export filename (override in implementing class)
     */
    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $title = $this->getExportTitle() ?? 'export';
        $sanitized = preg_replace('/[^a-z0-9]+/i', '_', $title);
        return strtolower($sanitized) . '_' . date('Y-m-d_His') . '.' . $extension;
    }

    /**
     * Get export metadata (override in implementing class)
     */
    protected function getExportMetadata(): array
    {
        return [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
        ];
    }
}

