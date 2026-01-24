<?php

namespace App\Services\Finance;

use App\Models\Finance\BranchTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BranchTransactionImportService
{
    public function import(string $filePath, array $config): ImportResult
    {
        $result = new ImportResult();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [
                    'reference_no' => $this->getCellValue($worksheet, $row, 'A'),
                    'transaction_date' => $this->getCellValue($worksheet, $row, 'B'),
                    'statement' => $this->getCellValue($worksheet, $row, 'C'),
                    'amount' => $this->getCellValue($worksheet, $row, 'D'),
                ];

                if ($this->isEmptyRow($rowData)) {
                    continue;
                }

                $error = $this->validateRow($rowData, $row, $config);
                if ($error) {
                    $result->failed++;
                    $result->errors[] = [
                        'row' => $row,
                        'reference_no' => $rowData['reference_no'] ?? '',
                        'error' => $error,
                        'values' => $rowData,
                    ];

                    if (!$config['allow_partial']) {
                        continue;
                    }
                    continue;
                }

                $normalizedDate = $this->normalizeDate(
                    $rowData['transaction_date'],
                    $config['default_transaction_date']
                );

                $normalizedAmount = $this->normalizeAmount($rowData['amount']);

                if ($normalizedAmount <= 0) {
                    $result->failed++;
                    $result->errors[] = [
                        'row' => $row,
                        'reference_no' => $rowData['reference_no'] ?? '',
                        'error' => 'Amount must be greater than 0',
                        'values' => $rowData,
                    ];
                    if (!$config['allow_partial']) {
                        continue;
                    }
                    continue;
                }

                $notes = trim($rowData['statement'] ?? '');
                if (!empty($config['global_notes'])) {
                    $notes = trim($config['global_notes']) . "\n" . $notes;
                }

                $existing = BranchTransaction::where('branch_id', $config['branch_id'])
                    ->where('finance_type_id', $config['finance_type_id'])
                    ->where('currency_id', $config['currency_id'])
                    ->where('reference_no', $rowData['reference_no'])
                    ->first();

                if ($existing) {
                    if ($config['on_duplicate'] === 'skip') {
                        $result->skipped++;
                        continue;
                    }

                    if ($config['on_duplicate'] === 'update') {
                        DB::transaction(function () use ($existing, $normalizedDate, $normalizedAmount, $notes, $config) {
                            $existing->update([
                                'trx_date' => $normalizedDate,
                                'amount' => $normalizedAmount,
                                'notes' => $notes,
                                'country_id' => $config['country_id'],
                                'payment_method' => $config['payment_method'],
                            ]);
                        });
                        $result->updated++;
                        $result->updatedIds[] = $existing->id;
                        continue;
                    }
                }

                $defaultStatus = $config['default_status'] ?? 'approved';
                $transaction = DB::transaction(function () use ($config, $rowData, $normalizedDate, $normalizedAmount, $notes, $defaultStatus) {
                    $transactionData = [
                        'trx_date' => $normalizedDate,
                        'branch_id' => $config['branch_id'],
                        'country_id' => $config['country_id'],
                        'currency_id' => $config['currency_id'],
                        'finance_type_id' => $config['finance_type_id'],
                        'amount' => $normalizedAmount,
                        'payment_method' => $config['payment_method'],
                        'reference_no' => $rowData['reference_no'],
                        'notes' => $notes,
                        'created_by' => auth()->id(),
                        'status' => $defaultStatus,
                    ];

                    if ($defaultStatus === 'approved') {
                        $transactionData['approved_by'] = auth()->id();
                        $transactionData['approved_at'] = now();
                    }

                    return BranchTransaction::create($transactionData);
                });

                $result->imported++;
                $result->importedIds[] = $transaction->id;
            }
        } catch (\Exception $e) {
            $result->failed++;
            $result->errors[] = [
                'row' => 0,
                'reference_no' => '',
                'error' => 'File error: ' . $e->getMessage(),
                'values' => [],
            ];
        }

        return $result;
    }

    protected function getCellValue($worksheet, int $row, string $column): ?string
    {
        $cell = $worksheet->getCell($column . $row);
        $value = $cell->getValue();

        if ($cell->getDataType() === 'f') {
            return $cell->getCalculatedValue();
        }

        if ($value === null) {
            return null;
        }

        return trim((string) $value);
    }

    protected function isEmptyRow(array $rowData): bool
    {
        return empty($rowData['reference_no']) &&
               empty($rowData['transaction_date']) &&
               empty($rowData['statement']) &&
               empty($rowData['amount']);
    }

    protected function validateRow(array $row, int $rowNumber, array $config): ?string
    {
        if (empty($row['reference_no'])) {
            return 'Reference number is required';
        }

        if (empty($row['statement'])) {
            return 'Statement is required';
        }

        if (empty($row['amount'])) {
            return 'Amount is required';
        }

        $amount = $this->normalizeAmount($row['amount']);
        if ($amount <= 0) {
            return 'Amount must be greater than 0';
        }

        if (!empty($row['transaction_date'])) {
            $date = $this->normalizeDate($row['transaction_date'], null);
            if (!$date) {
                return 'Invalid date format';
            }
        }

        return null;
    }

    protected function normalizeDate($value, ?string $default): ?Carbon
    {
        if (empty($value)) {
            if ($default) {
                return Carbon::parse($default);
            }
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject((float) $value));
            } catch (\Exception $e) {
                return null;
            }
        }

        $value = trim((string) $value);

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'Y/m/d',
            'm/d/Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date) {
                    return $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            if ($default) {
                return Carbon::parse($default);
            }
            return null;
        }
    }

    protected function normalizeAmount($value): float
    {
        if (empty($value)) {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);
        $value = str_replace(',', '', $value);
        $value = preg_replace('/[^\d.-]/', '', $value);

        return (float) $value;
    }
}

class ImportResult
{
    public int $imported = 0;
    public int $updated = 0;
    public int $skipped = 0;
    public int $failed = 0;
    public array $errors = [];
    public array $importedIds = [];
    public array $updatedIds = [];
}
