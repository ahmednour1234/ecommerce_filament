<?php

namespace App\Reports\DTOs;

/**
 * Filter Data Transfer Object
 * Standardizes filter parameters across all reports
 */
class FilterDTO
{
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public ?int $branchId = null;
    public ?int $costCenterId = null;
    public ?int $accountId = null;
    public ?int $currencyId = null;
    public bool $includeZeroRows = false;
    public bool $postedOnly = true;
    public ?int $projectId = null;
    public ?int $fiscalYearId = null;
    public ?int $periodId = null;
    public array $additional = [];

    public function __construct(array $data = [])
    {
        $this->fromDate = $data['from_date'] ?? $data['fromDate'] ?? null;
        $this->toDate = $data['to_date'] ?? $data['toDate'] ?? null;
        $this->branchId = $data['branch_id'] ?? $data['branchId'] ?? null;
        $this->costCenterId = $data['cost_center_id'] ?? $data['costCenterId'] ?? null;
        $this->accountId = $data['account_id'] ?? $data['accountId'] ?? null;
        $this->currencyId = $data['currency_id'] ?? $data['currencyId'] ?? null;
        $this->includeZeroRows = $data['include_zero_rows'] ?? $data['includeZeroRows'] ?? false;
        $this->postedOnly = $data['posted_only'] ?? $data['postedOnly'] ?? true;
        $this->projectId = $data['project_id'] ?? $data['projectId'] ?? null;
        $this->fiscalYearId = $data['fiscal_year_id'] ?? $data['fiscalYearId'] ?? null;
        $this->periodId = $data['period_id'] ?? $data['periodId'] ?? null;
        
        // Store any additional filters
        $standardKeys = [
            'from_date', 'fromDate', 'to_date', 'toDate',
            'branch_id', 'branchId', 'cost_center_id', 'costCenterId',
            'account_id', 'accountId', 'currency_id', 'currencyId',
            'include_zero_rows', 'includeZeroRows', 'posted_only', 'postedOnly',
            'project_id', 'projectId', 'fiscal_year_id', 'fiscalYearId',
            'period_id', 'periodId'
        ];
        
        foreach ($data as $key => $value) {
            if (!in_array($key, $standardKeys)) {
                $this->additional[$key] = $value;
            }
        }
    }

    /**
     * Get from date as Carbon instance
     */
    public function getFromDate(): ?\Carbon\Carbon
    {
        return $this->fromDate ? \Carbon\Carbon::parse($this->fromDate) : null;
    }

    /**
     * Get to date as Carbon instance
     */
    public function getToDate(): ?\Carbon\Carbon
    {
        return $this->toDate ? \Carbon\Carbon::parse($this->toDate) : null;
    }

    /**
     * Get from date as DateTime instance
     */
    public function getFromDateTime(): ?\DateTime
    {
        return $this->fromDate ? new \DateTime($this->fromDate) : null;
    }

    /**
     * Get to date as DateTime instance
     */
    public function getToDateTime(): ?\DateTime
    {
        return $this->toDate ? new \DateTime($this->toDate) : null;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'branch_id' => $this->branchId,
            'cost_center_id' => $this->costCenterId,
            'account_id' => $this->accountId,
            'currency_id' => $this->currencyId,
            'include_zero_rows' => $this->includeZeroRows,
            'posted_only' => $this->postedOnly,
            'project_id' => $this->projectId,
            'fiscal_year_id' => $this->fiscalYearId,
            'period_id' => $this->periodId,
            ...$this->additional,
        ];
    }
}

