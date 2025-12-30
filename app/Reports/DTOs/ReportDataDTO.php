<?php

namespace App\Reports\DTOs;

/**
 * Report Data Transfer Object
 * Standardizes report data structure
 */
class ReportDataDTO
{
    public array $rows = [];
    public array $totals = [];
    public array $summary = [];
    public array $metadata = [];

    public function __construct(array $data = [])
    {
        $this->rows = $data['rows'] ?? [];
        $this->totals = $data['totals'] ?? [];
        $this->summary = $data['summary'] ?? [];
        $this->metadata = $data['metadata'] ?? [];
    }

    /**
     * Add a row
     */
    public function addRow(array $row): void
    {
        $this->rows[] = $row;
    }

    /**
     * Set totals
     */
    public function setTotals(array $totals): void
    {
        $this->totals = $totals;
    }

    /**
     * Set summary
     */
    public function setSummary(array $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * Set metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'rows' => $this->rows,
            'totals' => $this->totals,
            'summary' => $this->summary,
            'metadata' => $this->metadata,
        ];
    }
}

