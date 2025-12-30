<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\Voucher;
use Illuminate\Database\Eloquent\Builder;

/**
 * Cash Flow Report Service (Direct Method)
 */
class CashFlowReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        // Cash flow uses vouchers (receipts and payments)
        return Voucher::query()
            ->with(['account', 'branch', 'costCenter']);
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyDateRange($query, 'voucher_date');
        $this->applyBranch($query);
        $this->applyCostCenter($query);

        $vouchers = $query->get();

        $rows = [];
        $totalCashIn = 0;
        $totalCashOut = 0;

        // Group by voucher type
        foreach ($vouchers as $voucher) {
            $amount = (float) $voucher->amount;
            
            if ($voucher->type === 'receipt') {
                $totalCashIn += $amount;
                $rows[] = [
                    'date' => $voucher->voucher_date->format('Y-m-d'),
                    'type' => trans_dash('reports.cash_flow.receipt', 'Receipt'),
                    'voucher_number' => $voucher->voucher_number,
                    'description' => $voucher->description ?? '',
                    'cash_in' => $amount,
                    'cash_out' => 0,
                ];
            } else {
                $totalCashOut += $amount;
                $rows[] = [
                    'date' => $voucher->voucher_date->format('Y-m-d'),
                    'type' => trans_dash('reports.cash_flow.payment', 'Payment'),
                    'voucher_number' => $voucher->voucher_number,
                    'description' => $voucher->description ?? '',
                    'cash_in' => 0,
                    'cash_out' => $amount,
                ];
            }
        }

        // Add opening balance (simplified - would need to calculate from previous period)
        $openingBalance = 0; // TODO: Calculate from previous period

        // Add summary rows
        $rows[] = [
            'date' => '',
            'type' => trans_dash('reports.cash_flow.opening_balance', 'Opening Balance'),
            'voucher_number' => '',
            'description' => '',
            'cash_in' => $openingBalance > 0 ? $openingBalance : 0,
            'cash_out' => $openingBalance < 0 ? abs($openingBalance) : 0,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'type' => trans_dash('reports.cash_flow.total_cash_in', 'Total Cash In'),
            'voucher_number' => '',
            'description' => '',
            'cash_in' => $totalCashIn,
            'cash_out' => 0,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'type' => trans_dash('reports.cash_flow.total_cash_out', 'Total Cash Out'),
            'voucher_number' => '',
            'description' => '',
            'cash_in' => 0,
            'cash_out' => $totalCashOut,
            '_is_total' => true,
        ];

        $netCashFlow = $totalCashIn - $totalCashOut;
        $closingBalance = $openingBalance + $netCashFlow;

        $rows[] = [
            'date' => '',
            'type' => trans_dash('reports.cash_flow.net_cash_flow', 'Net Cash Flow'),
            'voucher_number' => '',
            'description' => '',
            'cash_in' => $netCashFlow > 0 ? $netCashFlow : 0,
            'cash_out' => $netCashFlow < 0 ? abs($netCashFlow) : 0,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'type' => trans_dash('reports.cash_flow.closing_balance', 'Closing Balance'),
            'voucher_number' => '',
            'description' => '',
            'cash_in' => $closingBalance > 0 ? $closingBalance : 0,
            'cash_out' => $closingBalance < 0 ? abs($closingBalance) : 0,
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'total_amount' => $netCashFlow,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
        ]);

        $summary = [
            'opening_balance' => \App\Support\Money::format($openingBalance),
            'total_cash_in' => \App\Support\Money::format($totalCashIn),
            'total_cash_out' => \App\Support\Money::format($totalCashOut),
            'net_cash_flow' => \App\Support\Money::format($netCashFlow),
            'closing_balance' => \App\Support\Money::format($closingBalance),
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
        $title = trans_dash('reports.cash_flow.title', 'Cash Flow Statement');
        
        if ($this->filters->fromDate && $this->filters->toDate) {
            $title .= ' (' . $this->filters->fromDate . ' to ' . $this->filters->toDate . ')';
        }
        
        return $title;
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.cash_flow.date', 'Date'),
            trans_dash('reports.cash_flow.type', 'Type'),
            trans_dash('reports.cash_flow.voucher_number', 'Voucher #'),
            trans_dash('reports.cash_flow.description', 'Description'),
            trans_dash('reports.cash_flow.cash_in', 'Cash In'),
            trans_dash('reports.cash_flow.cash_out', 'Cash Out'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['date'],
                $row['type'],
                $row['voucher_number'],
                $row['description'],
                \App\Support\Money::format($row['cash_in']),
                \App\Support\Money::format($row['cash_out']),
            ];
        }, $rows);
    }
}

