<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\Asset;
use Illuminate\Database\Eloquent\Builder;

/**
 * Fixed Assets Report Service
 */
class FixedAssetsReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        return Asset::query()
            ->where('type', 'fixed')
            ->with(['account', 'branch', 'costCenter']);
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        
        if ($this->filters->branchId) {
            $query->where('branch_id', $this->filters->branchId);
        }
        
        if ($this->filters->costCenterId) {
            $query->where('cost_center_id', $this->filters->costCenterId);
        }

        $assets = $query->get();

        $rows = [];
        $totalAcquisition = 0;
        $totalDepreciation = 0;
        $totalNBV = 0;

        foreach ($assets as $asset) {
            $acquisition = (float) $asset->purchase_cost;
            $depreciation = (float) ($acquisition - $asset->current_value);
            $nbv = (float) $asset->current_value;

            $totalAcquisition += $acquisition;
            $totalDepreciation += $depreciation;
            $totalNBV += $nbv;

            $rows[] = [
                'code' => $asset->code,
                'name' => $asset->name,
                'category' => $asset->category,
                'purchase_date' => $asset->purchase_date?->format('Y-m-d') ?? '',
                'acquisition_cost' => $acquisition,
                'depreciation' => $depreciation,
                'net_book_value' => $nbv,
                'status' => $asset->status,
            ];
        }

        $rows[] = [
            'code' => '',
            'name' => 'TOTAL',
            'category' => '',
            'purchase_date' => '',
            'acquisition_cost' => $totalAcquisition,
            'depreciation' => $totalDepreciation,
            'net_book_value' => $totalNBV,
            'status' => '',
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'total_amount' => $totalNBV,
        ]);

        $summary = [
            'total_acquisition' => \App\Support\Money::format($totalAcquisition),
            'total_depreciation' => \App\Support\Money::format($totalDepreciation),
            'total_nbv' => \App\Support\Money::format($totalNBV),
        ];

        return new \App\Reports\DTOs\ReportDataDTO([
            'rows' => $rows,
            'totals' => $totals->toArray(),
            'summary' => $summary,
            'metadata' => $this->getMetadata(),
        ]);
    }

    protected function getReportTitle(): string
    {
        return trans_dash('reports.fixed_assets.title', 'Fixed Assets Report');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.fixed_assets.code', 'Code'),
            trans_dash('reports.fixed_assets.name', 'Name'),
            trans_dash('reports.fixed_assets.category', 'Category'),
            trans_dash('reports.fixed_assets.purchase_date', 'Purchase Date'),
            trans_dash('reports.fixed_assets.acquisition_cost', 'Acquisition Cost'),
            trans_dash('reports.fixed_assets.depreciation', 'Depreciation'),
            trans_dash('reports.fixed_assets.net_book_value', 'Net Book Value'),
            trans_dash('reports.fixed_assets.status', 'Status'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['code'],
                $row['name'],
                $row['category'],
                $row['purchase_date'],
                \App\Support\Money::format($row['acquisition_cost']),
                \App\Support\Money::format($row['depreciation']),
                \App\Support\Money::format($row['net_book_value']),
                $row['status'],
            ];
        }, $rows);
    }
}

