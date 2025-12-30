<?php

namespace App\Services\Reports;

use App\Reports\Base\ReportBase;
use App\Reports\DTOs\TotalsDTO;
use App\Models\Accounting\JournalEntryLine;
use Illuminate\Database\Eloquent\Builder;

/**
 * VAT Report Service
 * Note: This is a simplified implementation. Enhance with actual tax tables if available.
 */
class VatReportService extends ReportBase
{
    protected function buildQuery(): Builder
    {
        // Simplified: Use journal entry lines with VAT accounts
        // In a real system, you'd have a taxes table
        return JournalEntryLine::query()
            ->whereHas('account', function($q) {
                $q->where('code', 'like', '%VAT%')
                  ->orWhere('name', 'like', '%VAT%')
                  ->orWhere('name', 'like', '%ضريبة%');
            })
            ->with(['account', 'journalEntry']);
    }

    public function getData(): \App\Reports\DTOs\ReportDataDTO
    {
        $query = $this->buildQuery();
        $this->applyDateRange($query, 'created_at');
        $this->applyBranch($query);
        
        if ($this->filters->postedOnly) {
            $query->whereHas('journalEntry', fn($q) => $q->where('is_posted', true));
        }

        $lines = $query->get();

        $rows = [];
        $outputVat = 0;
        $inputVat = 0;

        foreach ($lines as $line) {
            $amount = (float) ($line->debit > 0 ? $line->debit : $line->credit);
            
            // Simplified logic: credits are usually output VAT, debits are input VAT
            if ($line->credit > 0) {
                $outputVat += $amount;
            } else {
                $inputVat += $amount;
            }

            $rows[] = [
                'date' => $line->journalEntry->entry_date->format('Y-m-d'),
                'account_code' => $line->account->code,
                'account_name' => $line->account->name,
                'entry_number' => $line->journalEntry->entry_number,
                'output_vat' => $line->credit > 0 ? $amount : 0,
                'input_vat' => $line->debit > 0 ? $amount : 0,
            ];
        }

        $netVat = $outputVat - $inputVat;

        $rows[] = [
            'date' => '',
            'account_code' => '',
            'account_name' => trans_dash('reports.vat.total_output_vat', 'Total Output VAT'),
            'entry_number' => '',
            'output_vat' => $outputVat,
            'input_vat' => 0,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'account_code' => '',
            'account_name' => trans_dash('reports.vat.total_input_vat', 'Total Input VAT'),
            'entry_number' => '',
            'output_vat' => 0,
            'input_vat' => $inputVat,
            '_is_total' => true,
        ];

        $rows[] = [
            'date' => '',
            'account_code' => '',
            'account_name' => trans_dash('reports.vat.net_vat', 'Net VAT'),
            'entry_number' => '',
            'output_vat' => $netVat > 0 ? $netVat : 0,
            'input_vat' => $netVat < 0 ? abs($netVat) : 0,
            '_is_total' => true,
        ];

        $totals = new TotalsDTO([
            'total_amount' => $netVat,
        ]);

        $summary = [
            'output_vat' => \App\Support\Money::format($outputVat),
            'input_vat' => \App\Support\Money::format($inputVat),
            'net_vat' => \App\Support\Money::format($netVat),
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
        return trans_dash('reports.vat.title', 'VAT Report');
    }

    protected function getExportHeaders(): array
    {
        return [
            trans_dash('reports.vat.date', 'Date'),
            trans_dash('reports.vat.account_code', 'Account Code'),
            trans_dash('reports.vat.account_name', 'Account Name'),
            trans_dash('reports.vat.entry_number', 'Entry #'),
            trans_dash('reports.vat.output_vat', 'Output VAT'),
            trans_dash('reports.vat.input_vat', 'Input VAT'),
        ];
    }

    protected function prepareRowsForExport(array $rows): array
    {
        return array_map(function ($row) {
            return [
                $row['date'],
                $row['account_code'],
                $row['account_name'],
                $row['entry_number'],
                \App\Support\Money::format($row['output_vat']),
                \App\Support\Money::format($row['input_vat']),
            ];
        }, $rows);
    }
}

