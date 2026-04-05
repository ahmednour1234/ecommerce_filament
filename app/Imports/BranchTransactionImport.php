<?php

namespace App\Imports;

use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BranchTransactionImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $successCount = 0;

    public function collection(Collection $rows)
    {
        $user = auth()->user();
        $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
        if (!empty($user?->branch_id)) {
            $userBranches[] = (int) $user->branch_id;
        }
        $userBranches = array_values(array_unique(array_filter($userBranches)));

        foreach ($rows as $index => $row) {
            try {
                $rowArray = is_array($row) ? $row : $row->toArray();

                // Skip empty rows
                if (collect($rowArray)->filter(fn($value) => $value !== null && $value !== '')->isEmpty()) {
                    continue;
                }

                $data = $this->mapRowData($rowArray, $user, $userBranches);

                if (!empty($data)) {
                    BranchTransaction::create($data);
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2, // +2 for header row and 0-based index
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    protected function mapRowData(array $row, $user, array $userBranches): array
    {
        $data = [];

        // Map branch
        if (!empty($row['branch_id'] ?? $row['branch'])) {
            $branchKey = $row['branch_id'] ?? $row['branch'];
            $branch = is_numeric($branchKey)
                ? Branch::find($branchKey)
                : Branch::where('name_ar', $branchKey)->orWhere('name_en', $branchKey)->first();

            if ($branch && (in_array($branch->id, $userBranches) || $user->hasRole('super_admin'))) {
                $data['branch_id'] = $branch->id;
            } else {
                throw new \Exception('Branch not found or unauthorized');
            }
        } else {
            throw new \Exception('Branch ID or name is required');
        }

        // Map transaction date
        if (!empty($row['trx_date'] ?? $row['transaction_date'])) {
            $data['trx_date'] = $this->parseDate($row['trx_date'] ?? $row['transaction_date']);
        } else {
            $data['trx_date'] = now()->toDateString();
        }

        // Map country
        if (!empty($row['country_id'] ?? $row['country'])) {
            $countryKey = $row['country_id'] ?? $row['country'];
            $country = is_numeric($countryKey)
                ? Country::find($countryKey)
                : Country::where('name_ar', $countryKey)
                    ->orWhere('name_en', $countryKey)
                    ->orWhere('iso2', $countryKey)
                    ->first();

            if ($country) {
                $data['country_id'] = $country->id;
            }
        }

        // Map currency
        if (!empty($row['currency_id'] ?? $row['currency'])) {
            $currencyKey = $row['currency_id'] ?? $row['currency'];
            $currency = is_numeric($currencyKey)
                ? Currency::find($currencyKey)
                : Currency::where('code', strtoupper($currencyKey))
                    ->orWhere('name_ar', $currencyKey)
                    ->orWhere('name_en', $currencyKey)
                    ->first();

            if ($currency) {
                $data['currency_id'] = $currency->id;
            }
        }

        // Map finance type
        if (!empty($row['finance_type_id'] ?? $row['finance_type'])) {
            $typeKey = $row['finance_type_id'] ?? $row['finance_type'];
            $financeType = is_numeric($typeKey)
                ? FinanceType::find($typeKey)
                : FinanceType::where('name_ar', $typeKey)
                    ->orWhere('name_en', $typeKey)
                    ->first();

            if ($financeType) {
                $data['finance_type_id'] = $financeType->id;
            }
        }

        // Map amount
        if (!empty($row['amount'])) {
            $data['amount'] = (float) str_replace(',', '', $row['amount']);
        } else {
            throw new \Exception('Amount is required');
        }

        // Map payment method (optional)
        if (!empty($row['payment_method'])) {
            $data['payment_method'] = $row['payment_method'];
        }

        // Map recipient name (optional)
        if (!empty($row['recipient_name'])) {
            $data['recipient_name'] = $row['recipient_name'];
        }

        // Map reference number (optional)
        if (!empty($row['reference_no'])) {
            $data['reference_no'] = $row['reference_no'];
        }

        // Map notes (optional)
        if (!empty($row['notes'])) {
            $data['notes'] = $row['notes'];
        }

        // Set default values
        $data['created_by'] = $user->id;
        $data['status'] = 'pending';

        return $data;
    }

    protected function parseDate($dateValue): string
    {
        if ($dateValue instanceof \DateTime) {
            return $dateValue->format('Y-m-d');
        }

        // Handle Excel date serial numbers
        if (is_numeric($dateValue)) {
            $excelDate = intval($dateValue);
            // Excel epoch is 1900-01-01
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays($excelDate - 2); // -2 for Excel's leap year bug
            return $date->format('Y-m-d');
        }

        // Try parsing string dates
        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d', $dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $dateValue)->format('Y-m-d');
            } catch (\Exception $e) {
                return now()->toDateString();
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
