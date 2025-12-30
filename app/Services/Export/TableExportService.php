<?php

namespace App\Services\Export;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class TableExportService
{
    /**
     * Extract table data from a query builder
     */
    public function getTableData(Builder $query, array $columns, ?int $limit = null): Collection
    {
        $query = clone $query;
        
        if ($limit) {
            $query->limit($limit);
        }
        
        $records = $query->get();
        
        return $this->formatRecords($records, $columns);
    }

    /**
     * Format records for export
     */
    protected function formatRecords(Collection $records, array $columns): Collection
    {
        return $records->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $column) {
                $key = $column['name'] ?? $column;
                $label = $column['label'] ?? $key;
                $value = $this->getColumnValue($record, $key);
                $row[$label] = $value;
            }
            return $row;
        });
    }

    /**
     * Get column value from record
     */
    protected function getColumnValue($record, string $key): mixed
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
            return $record->$key ?? $record->getAttribute($key) ?? '';
        }

        return $record[$key] ?? '';
    }

    /**
     * Format data for Excel export
     */
    public function formatForExcel(Collection $data): array
    {
        if ($data->isEmpty()) {
            return [];
        }

        $headers = array_keys($data->first());
        $rows = $data->map(fn($row) => array_values($row))->toArray();

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * Format data for PDF export
     */
    public function formatForPdf(Collection $data, string $title = 'Report', array $metadata = []): array
    {
        if ($data->isEmpty()) {
            return [
                'title' => $title,
                'headers' => [],
                'rows' => [],
                'metadata' => $metadata,
            ];
        }

        $headers = array_keys($data->first());
        $rows = $data->map(fn($row) => array_values($row))->toArray();

        return [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'metadata' => $metadata,
        ];
    }

    /**
     * Format monetary values
     */
    public function formatMoney($value, string $currency = 'USD'): string
    {
        if (!is_numeric($value)) {
            return $value;
        }

        return number_format((float) $value, 2) . ' ' . $currency;
    }

    /**
     * Format date values
     */
    public function formatDate($value, string $format = 'Y-m-d'): string
    {
        if (!$value) {
            return '';
        }

        if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
            return $value->format($format);
        }

        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return (string) $value;
        }
    }
}

