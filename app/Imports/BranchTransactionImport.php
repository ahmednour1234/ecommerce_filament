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
    protected ?int $branch_id = null;
    protected ?int $finance_type_id = null;
    protected ?int $currency_id = null;
    protected ?int $country_id = null;
    protected ?string $default_date = null;
    protected ?string $payment_method = null;
    protected ?string $notes = null;
    protected bool $allow_partial = true;

    public function __construct(
        ?int $branch_id = null,
        ?int $finance_type_id = null,
        ?int $currency_id = null,
        ?int $country_id = null,
        ?string $default_date = null,
        ?string $payment_method = null,
        ?string $notes = null,
        bool $allow_partial = true,
    ) {
        $this->branch_id = $branch_id;
        $this->finance_type_id = $finance_type_id;
        $this->currency_id = $currency_id;
        $this->country_id = $country_id;
        $this->default_date = $default_date;
        $this->payment_method = $payment_method;
        $this->notes = $notes;
        $this->allow_partial = $allow_partial;
    }

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
                if (!$this->allow_partial) {
                    throw $e;
                }
                $this->errors[] = [
                    'row' => $index + 2,
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    protected function mapRowData(array $row, $user, array $userBranches): array
    {
        $data = [];

        // Map branch - use form default if provided
        if ($this->branch_id) {
            $data['branch_id'] = $this->branch_id;
        } elseif (!empty($row['branch_id'] ?? $row['branch'])) {
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
        } elseif ($this->default_date) {
            $data['trx_date'] = $this->parseDate($this->default_date);
        } else {
            $data['trx_date'] = now()->toDateString();
        }

        // Map country - use form default if provided
        if ($this->country_id) {
            $data['country_id'] = $this->country_id;
        } elseif (!empty($row['country_id'] ?? $row['country'])) {
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

        // Map currency - use form default if provided
        if ($this->currency_id) {
            $data['currency_id'] = $this->currency_id;
        } elseif (!empty($row['currency_id'] ?? $row['currency'])) {
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

        // Map finance type - use form default if provided
        if ($this->finance_type_id) {
            $data['finance_type_id'] = $this->finance_type_id;
        } elseif (!empty($row['finance_type_id'] ?? $row['finance_type'])) {
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

        // Map payment method - use form default if not in row
        if (!empty($row['payment_method'])) {
            $data['payment_method'] = $row['payment_method'];
        } elseif ($this->payment_method) {
            $data['payment_method'] = $this->payment_method;
        }

        // Map recipient name (optional)
        if (!empty($row['recipient_name'])) {
            $data['recipient_name'] = $row['recipient_name'];
        }

        // Map reference number (optional)
        if (!empty($row['reference_no'])) {
            $data['reference_no'] = $row['reference_no'];
        }

        // Map notes - use form default if not in row
        if (!empty($row['notes'])) {
            $data['notes'] = $row['notes'];
        } elseif ($this->notes) {
            $data['notes'] = $this->notes;
        }

        // Set default values
        $data['created_by'] = auth()->user()->id;
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
