<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Sales\Customer;
use App\Models\Sales\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Accounts Receivable (A/R Customers) Report Service
 */
class AccountsReceivableReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        // Use Customer model as base
        return Customer::query();
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $customers = Customer::active()->with('invoices')->get();

        $rows = [];
        $totalBalance = 0;

        foreach ($customers as $customer) {
            // Calculate balance from invoices (unpaid invoices)
            $query = Invoice::where('customer_id', $customer->id);
            
            if ($this->filters->toDate) {
                $query->whereDate('invoice_date', '<=', $this->filters->toDate);
            }
            
            // Sum unpaid invoices (status != 'paid' or paid_at is null)
            $balance = $query->where(function($q) {
                $q->where('status', '!=', 'paid')
                  ->orWhereNull('paid_at');
            })->sum('total');

            if (!$this->filters->includeZeroRows && $balance == 0) {
                continue;
            }

            $totalBalance += $balance;

            $rows[] = [
                'customer_code' => $customer->code,
                'customer_name' => $customer->name,
                'balance' => $balance,
                'credit_limit' => $customer->credit_limit,
            ];
        }

        $rows[] = [
            'customer_code' => '',
            'customer_name' => 'TOTAL',
            'balance' => $totalBalance,
            'credit_limit' => 0,
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'total_amount' => $totalBalance,
        ]);

        $summary = [
            'total_receivable' => \App\Support\Money::format($totalBalance),
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
        return trans_dash('reports.accounts_receivable.title', 'Accounts Receivable Report');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.accounts_receivable.customer_code', 'Customer Code'),
            trans_dash('reports.accounts_receivable.customer_name', 'Customer Name'),
            trans_dash('reports.accounts_receivable.balance', 'Balance'),
            trans_dash('reports.accounts_receivable.credit_limit', 'Credit Limit'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['customer_code'],
                $row['customer_name'],
                \App\Support\Money::format($row['balance']),
                \App\Support\Money::format($row['credit_limit']),
            ];
        }, $rows);
    }
}

