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

        $vouchers = $query->with(['account', 'journalEntry'])->get();

        $rows = [];
        $totalCashIn = 0;
        $totalCashOut = 0;

        // Group by voucher type
        foreach ($vouchers as $voucher) {
            $amount = (float) $voucher->amount;
            $accountCode = $voucher->account ? $voucher->account->code : '';
            $entryNumber = $voucher->journalEntry ? $voucher->journalEntry->entry_number : '';
            $reference = $voucher->reference ?? ($voucher->journalEntry ? $voucher->journalEntry->reference : '');
            
            if ($voucher->type === 'receipt') {
                $totalCashIn += $amount;
                $rows[] = [
                    'account_code' => $accountCode,
                    'date' => $voucher->voucher_date->format('Y-m-d'),
                    'entry_number' => $entryNumber,
                    'reference' => $reference,
                    'description' => $voucher->description ?? '',
                    'debit' => 0,
                    'credit' => $amount,
                ];
            } else {
                $totalCashOut += $amount;
                $rows[] = [
                    'account_code' => $accountCode,
                    'date' => $voucher->voucher_date->format('Y-m-d'),
                    'entry_number' => $entryNumber,
                    'reference' => $reference,
                    'description' => $voucher->description ?? '',
                    'debit' => $amount,
                    'credit' => 0,
                ];
            }
        }

        // Add opening balance (simplified - would need to calculate from previous period)
        $openingBalance = 0; // TODO: Calculate from previous period

        // Add summary rows
        $netCashFlow = $totalCashIn - $totalCashOut;
        $closingBalance = $openingBalance + $netCashFlow;

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
            trans_dash('reports.cash_flow.account_code', 'Account Code'),
            trans_dash('reports.cash_flow.date', 'Date'),
            trans_dash('reports.cash_flow.entry_number', 'Entry Number'),
            trans_dash('reports.cash_flow.reference', 'Reference'),
            trans_dash('reports.cash_flow.description', 'Description'),
            trans_dash('reports.cash_flow.debit', 'Debit'),
            trans_dash('reports.cash_flow.credit', 'Credit'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['account_code'] ?? '',
                $row['date'] ?? '',
                $row['entry_number'] ?? '',
                $row['reference'] ?? '',
                $row['description'] ?? '',
                \App\Support\Money::format($row['debit'] ?? 0),
                \App\Support\Money::format($row['credit'] ?? 0),
            ];
        }, $rows);
    }
}

