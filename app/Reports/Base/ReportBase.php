<?php

namespace App\Reports\Base;

use App\Exports\ReportPdfExport;
use App\Exports\TableExport;
use App\Reports\DTOs\FilterDTO;
use App\Reports\DTOs\ReportDataDTO;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Abstract Base Class for All Reports
 * Provides common functionality for building queries, applying filters, calculating totals, and exporting
 */
abstract class ReportBase
{
    protected FilterDTO $filters;
    protected ?int $baseCurrencyId = null;

    public function __construct(FilterDTO $filters)
    {
        $this->filters = $filters;
        $this->baseCurrencyId = \App\Support\Money::defaultCurrencyId();
    }

    /**
     * Build the main query for the report
     * Must be implemented by each report
     */
    abstract protected function buildQuery(): Builder;

    /**
     * Get report data
     * Can be overridden by specific reports
     */
    public function getData(): ReportDataDTO
    {
        $query = $this->buildQuery();
        $rows = $this->formatRows($query->get());
        $totals = $this->calculateTotals($rows);
        $summary = $this->calculateSummary($rows, $totals);

        return new ReportDataDTO([
            'rows' => $rows,
            'totals' => $totals->toArray(),
            'summary' => $summary,
            'metadata' => $this->getMetadata(),
        ]);
    }

    /**
     * Format rows for display
     * Can be overridden by specific reports
     */
    protected function formatRows(Collection $data): array
    {
        return $data->toArray();
    }

    /**
     * Calculate totals
     * Can be overridden by specific reports
     */
    protected function calculateTotals(array $rows): TotalsDTO
    {
        $totalDebit = 0;
        $totalCredit = 0;
        $totalAmount = 0;

        foreach ($rows as $row) {
            if (is_array($row)) {
                $totalDebit += (float) ($row['debit'] ?? $row['debits'] ?? 0);
                $totalCredit += (float) ($row['credit'] ?? $row['credits'] ?? 0);
                $totalAmount += (float) ($row['amount'] ?? 0);
            }
        }

        return new TotalsDTO([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Calculate summary cards
     * Can be overridden by specific reports
     */
    protected function calculateSummary(array $rows, TotalsDTO $totals): array
    {
        return [
            'total_debit' => \App\Support\Money::format($totals->totalDebit),
            'total_credit' => \App\Support\Money::format($totals->totalCredit),
            'net_balance' => \App\Support\Money::format($totals->getNetBalance()),
        ];
    }

    /**
     * Get report metadata
     */
    protected function getMetadata(): array
    {
        $metadata = [
            'from_date' => $this->filters->fromDate,
            'to_date' => $this->filters->toDate,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()?->name ?? 'System',
        ];

        if ($this->filters->branchId) {
            $branch = Branch::find($this->filters->branchId);
            $metadata['branch'] = $branch?->name;
        }

        if ($this->filters->costCenterId) {
            $costCenter = CostCenter::find($this->filters->costCenterId);
            $metadata['cost_center'] = $costCenter?->name;
        }

        if ($this->filters->accountId) {
            $account = \App\Models\Accounting\Account::find($this->filters->accountId);
            $metadata['account'] = $account ? ($account->code . ' - ' . $account->name) : null;
        }

        if ($this->filters->currencyId) {
            $currency = Currency::find($this->filters->currencyId);
            $metadata['currency'] = $currency?->name;
        }

        return $metadata;
    }

    /**
     * Apply standard filters to query
     */
    protected function applyFilters(Builder $query): Builder
    {
        // Date range - try common date column names
        if ($this->filters->fromDate) {
            $dateColumn = $this->getDateColumn($query);
            if ($dateColumn) {
                $query->whereDate($dateColumn, '>=', $this->filters->fromDate);
            }
        }

        if ($this->filters->toDate) {
            $dateColumn = $this->getDateColumn($query);
            if ($dateColumn) {
                $query->whereDate($dateColumn, '<=', $this->filters->toDate);
            }
        }

        // Branch
        if ($this->filters->branchId && $this->hasColumn($query, 'branch_id')) {
            $query->where('branch_id', $this->filters->branchId);
        }

        // Cost Center
        if ($this->filters->costCenterId && $this->hasColumn($query, 'cost_center_id')) {
            $query->where('cost_center_id', $this->filters->costCenterId);
        }

        // Account
        if ($this->filters->accountId && $this->hasColumn($query, 'account_id')) {
            $query->where('account_id', $this->filters->accountId);
        }

        // Posted only - only apply if query has journalEntry relationship
        if ($this->filters->postedOnly) {
            $model = $query->getModel();
            if (method_exists($model, 'journalEntry')) {
                $query->whereHas('journalEntry', function ($q) {
                    $q->where('is_posted', true);
                });
            } elseif ($this->hasColumn($query, 'is_posted')) {
                $query->where('is_posted', true);
            }
        }

        return $query;
    }

    /**
     * Get date column name for the query
     */
    protected function getDateColumn(Builder $query): ?string
    {
        $model = $query->getModel();
        $dateColumns = ['entry_date', 'created_at', 'date', 'voucher_date', 'invoice_date', 'purchase_date'];
        
        foreach ($dateColumns as $column) {
            if ($this->hasColumn($query, $column)) {
                return $column;
            }
        }
        
        return null;
    }

    /**
     * Check if query has a column
     */
    protected function hasColumn(Builder $query, string $column): bool
    {
        try {
            $model = $query->getModel();
            $table = $model->getTable();
            return Schema::hasColumn($table, $column);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Apply date range filter
     */
    protected function applyDateRange(Builder $query, string $dateColumn = 'entry_date'): Builder
    {
        if ($this->filters->fromDate) {
            $query->whereDate($dateColumn, '>=', $this->filters->fromDate);
        }

        if ($this->filters->toDate) {
            $query->whereDate($dateColumn, '<=', $this->filters->toDate);
        }

        return $query;
    }

    /**
     * Apply branch filter
     */
    protected function applyBranch(Builder $query): Builder
    {
        if ($this->filters->branchId) {
            $query->where('branch_id', $this->filters->branchId);
        }

        return $query;
    }

    /**
     * Apply cost center filter
     */
    protected function applyCostCenter(Builder $query): Builder
    {
        if ($this->filters->costCenterId) {
            $query->where('cost_center_id', $this->filters->costCenterId);
        }

        return $query;
    }

    /**
     * Apply currency filter and conversion
     */
    protected function applyCurrency(Builder $query): Builder
    {
        if ($this->filters->currencyId && $this->filters->currencyId !== $this->baseCurrencyId) {
            // If filtering by non-base currency, we need to handle conversion
            // This is report-specific, so reports should override if needed
        }

        return $query;
    }

    /**
     * Validate accounting integrity
     * Must be implemented by reports that require balance validation
     */
    public function validateIntegrity(): array
    {
        return [
            'is_valid' => true,
            'errors' => [],
            'warnings' => [],
        ];
    }

    /**
     * Export to Excel
     */
    public function exportExcel(string $filename = null): BinaryFileResponse
    {
        $data = $this->getData();
        $filename = $filename ?? $this->getExportFilename('xlsx');
        
        $headers = $this->getExportHeaders();
        $rows = $this->prepareRowsForExport($data->rows);

        $export = new TableExport($rows, $headers, $this->getReportTitle());
        
        return Excel::download($export, $filename);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(string $filename = null): \Illuminate\Http\Response
    {
        $data = $this->getData();
        $filename = $filename ?? $this->getExportFilename('pdf');
        
        $headers = $this->getExportHeaders();
        $rows = $this->prepareRowsForExport($data->rows);
        
        $isRtl = app()->getLocale() === 'ar';
        $metadata = array_merge($data->metadata, $this->getMetadata());
        
        $export = new ReportPdfExport(
            collect($rows),
            $headers,
            $this->getReportTitle(),
            $metadata,
            $isRtl,
            $this->getPdfView()
        );
        
        return $export->download($filename);
    }

    /**
     * Get print view data
     */
    public function getPrintViewData(): array
    {
        $data = $this->getData();
        
        return [
            'title' => $this->getReportTitle(),
            'headers' => $this->getExportHeaders(),
            'rows' => $this->prepareRowsForExport($data->rows),
            'summary' => $data->summary,
            'metadata' => array_merge($data->metadata, $this->getMetadata()),
        ];
    }

    /**
     * Get export headers
     * Can be overridden by specific reports
     */
    protected function getExportHeaders(): array
    {
        return [];
    }

    /**
     * Prepare rows for export
     * Can be overridden by specific reports
     */
    protected function prepareRowsForExport(array $rows): array
    {
        return $rows;
    }

    /**
     * Get report title
     * Must be implemented by each report
     */
    abstract protected function getReportTitle(): string;

    /**
     * Get PDF view name
     * Can be overridden by specific reports
     */
    protected function getPdfView(): string
    {
        return 'reports.pdf';
    }

    /**
     * Get export filename
     */
    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $title = $this->getReportTitle();
        $sanitized = preg_replace('/[^a-z0-9]+/i', '_', $title);
        return strtolower($sanitized) . '_' . date('Y-m-d_His') . '.' . $extension;
    }
}

