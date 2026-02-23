<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use App\Models\MainCore\Branch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class RecruitmentContractsImport implements ToCollection, WithHeadingRow
{
    protected array $errors = [];
    protected int $successCount = 0;
    protected int $skippedCount = 0;

    protected ?Country $defaultCountry;
    protected ?Currency $defaultCurrency;
    protected ?Nationality $defaultNationality;
    protected ?Profession $defaultProfession;
    protected int $defaultUserId;

    protected bool $hasPaymentStatus = false;
    protected bool $hasPaymentStatusCode = false;
    protected bool $hasIsPaid = false;
    protected bool $hasPaidAt = false;

    protected int $debugLoggedRows = 0;

    public function __construct()
    {
        $this->defaultCountry     = Country::where('is_active', true)->first() ?? Country::first();
        $this->defaultCurrency    = Currency::first();
        $this->defaultNationality = Nationality::where('is_active', true)->first() ?? Nationality::first();
        $this->defaultProfession  = Profession::where('is_active', true)->first() ?? Profession::first();
        $this->defaultUserId      = Auth::check() ? (int) Auth::id() : (int) config('app.default_user_id', 1);

        $this->checkSchema();
    }

    protected function checkSchema(): void
    {
        $table = 'recruitment_contracts';

        $this->hasPaymentStatus     = Schema::hasColumn($table, 'payment_status');
        $this->hasPaymentStatusCode = Schema::hasColumn($table, 'payment_status_code');
        $this->hasIsPaid            = Schema::hasColumn($table, 'is_paid');
        $this->hasPaidAt            = Schema::hasColumn($table, 'paid_at');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelRowNumber = $index + 2; // +1 heading +1 zero index
            try {
                $rowArray = is_array($row) ? $row : $row->toArray();

                if (!$this->rowHasData($rowArray)) {
                    $this->skippedCount++;
                    continue;
                }

                // ===== Extract fields =====
                $workerName = $this->getValue($rowArray, ['name_of_the_worker', 'worker_name', 'name', 'الاسم', 'اسم العامل']);
                $passportNo = $this->getValue($rowArray, ['passport_no', 'passport_number', 'passport', 'رقم الجواز']);
                $clientName = $this->getValue($rowArray, ['client_name', 'client', 'العميل', 'اسم العميل']);
                $sponsorName = $this->getValue($rowArray, ['sponsor_name', 'sponsor', 'الكفيل', 'اسم الكفيل']);
                $branchName  = $this->getValue($rowArray, ['branch_name', 'branch', 'الفرع', 'اسم الفرع']);
                $visaNo      = $this->getValue($rowArray, ['visa_no', 'visa_number', 'visa', 'رقم التأشيرة']);
                $idNumber    = $this->getValue($rowArray, ['id_number', 'id', 'national_id', 'رقم الهوية', 'ID number']);
                $note        = $this->getValue($rowArray, ['note', 'notes', 'ملاحظات', 'ملاحظة']);
                $arrivalDate = $this->getValue($rowArray, ['arrival_date', 'arrival', 'تاريخ الوصول']);
                $issueDate   = $this->getValue($rowArray, ['issue_date', 'issue', 'تاريخ الإصدار']);
                $statusCode  = $this->getValue($rowArray, ['status_code', 'status', 'الحالة']);

                // مهم: عمود الدفع عندك طويل في الإكسل، فهنا بنديله مفاتيح كثيرة + matching ذكي
                $paymentStatusRaw = $this->getValue($rowArray, [
                    'payment_status_code',
                    'payment_status',
                    'payment status',
                    'حالة الدفع',
                    'حالة_الدفع',
                    'payment',
                ]);

                $airportName = $this->getValue($rowArray, ['name_of_the_airport', 'airport', 'اسم المطار']);

                $workerName = $workerName ? trim((string) $workerName) : null;
                $passportNo = $passportNo ? trim((string) $passportNo) : null;

                if (empty($workerName) && empty($passportNo)) {
                    $this->addError($excelRowNumber, 'missing worker identity (name/passport)');
                    $this->skippedCount++;
                    continue;
                }

                // ===== Find/create relations =====
                $worker = $this->findOrCreateWorker($workerName, $passportNo, $sponsorName);
                if (!$worker) {
                    $this->addError($excelRowNumber, 'could not create/find worker');
                    $this->skippedCount++;
                    continue;
                }

                $client = $this->findOrCreateClient($clientName, $idNumber);
                $this->findOrCreateAgent($sponsorName); // ensures sponsor exists if needed
                $branch = $this->findOrCreateBranch($branchName);

                // ===== Visa no normalization =====
                $visaNoValue = $this->normalizeVisaNo($visaNo);
                if (empty($visaNoValue)) {
                    $visaNoValue = $this->generateDeterministicVisaNo($passportNo, $workerName, $index);
                }

                // ===== Map payment status =====
                $paymentStatus = $this->mapPaymentStatus($paymentStatusRaw);

                // ===== Build contract data =====
                $arrivalCountryId     = $this->mapCountryIdByName($airportName);
                $departureCountryId   = $this->mapCountryIdByName($airportName);
                $receivingStationId   = $this->mapReceivingStationIdByName($airportName);

                $contractData = [
                    'client_id' => $client?->id,
                    'branch_id' => $branch?->id,
                    'worker_id' => $worker->id,
                    'visa_no' => $visaNoValue,
                    'notes' => $note ? trim((string) $note) : null,
                    'status' => $this->mapStatus($statusCode),
                    'arrival_country_id' => $arrivalCountryId,
                    'departure_country_id' => $departureCountryId,
                    'receiving_station_id' => $receivingStationId,
                    'gregorian_request_date' => $arrivalDate ? ($this->parseDate($arrivalDate) ?? now()) : now(),
                    'visa_date' => $issueDate ? $this->parseDate($issueDate) : null,
                    'created_by' => $this->defaultUserId,
                ];

                $contractData = $this->applyPaymentStatus($contractData, $paymentStatus);

                // ===== Persist (IMPORTANT: use forceFill to bypass fillable issues) =====
                $contract = RecruitmentContract::firstOrNew(['visa_no' => $visaNoValue]);
                $contract->forceFill($contractData);
                $contract->save();

                // ===== Debug (optional) =====
                $this->debugLog($excelRowNumber, $paymentStatusRaw, $paymentStatus, $contractData);

                $this->successCount++;
            } catch (\Throwable $e) {
                $this->addError($excelRowNumber, $e->getMessage());
                $this->skippedCount++;
            }
        }
    }

    // ===================== Helpers =====================

    protected function rowHasData(array $rowArray): bool
    {
        foreach ($rowArray as $value) {
            $v = is_null($value) ? '' : trim((string) $value);
            if ($v !== '') return true;
        }
        return false;
    }

    protected function normalizeKey(string $key): string
    {
        $normalized = mb_strtolower($key, 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}_]+/u', '_', $normalized);
        $normalized = preg_replace('/_+/', '_', $normalized);
        return trim($normalized, '_');
    }

    protected function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey((string) $key);

            // direct
            if (array_key_exists($key, $row)) {
                $value = trim((string) $row[$key]);
                if ($value !== '') return $value;
            }

            // sanitized ascii-ish key
            $sanitizedKey = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $key));
            if (array_key_exists($sanitizedKey, $row)) {
                $value = trim((string) $row[$sanitizedKey]);
                if ($value !== '') return $value;
            }

            // fuzzy matching
            foreach ($row as $rowKey => $rowValue) {
                $normalizedRowKey = $this->normalizeKey((string) $rowKey);
                $value = trim((string) $rowValue);
                if ($value === '') continue;

                // exact
                if ($normalizedRowKey === $normalizedKey) return $value;

                // starts with (important for your long payment column)
                if (str_starts_with($normalizedRowKey, $normalizedKey)) return $value;

                // contains (for longer keys only)
                if (strlen($normalizedKey) >= 5 && str_contains($normalizedRowKey, $normalizedKey)) return $value;
            }
        }
        return null;
    }

    protected function mapCountryIdByName(?string $name): ?int
    {
        if (empty($name)) return null;

        try {
            $name = trim($name);

            $country = Country::where('is_active', true)
                ->where(function ($query) use ($name) {
                    $query->whereRaw('JSON_EXTRACT(name, "$.en") = ?', [$name])
                        ->orWhereRaw('JSON_EXTRACT(name, "$.ar") = ?', [$name])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) = LOWER(?)', [$name])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) = LOWER(?)', [$name]);
                })
                ->first();

            return $country?->id;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function mapReceivingStationIdByName(?string $name): ?int
    {
        if (empty($name)) return null;

        try {
            $stationClass = 'App\Models\Recruitment\ReceivingStation';
            if (class_exists($stationClass)) {
                $station = $stationClass::where('name', $name)
                    ->orWhere('name_ar', $name)
                    ->orWhere('name_en', $name)
                    ->first();

                return $station?->id;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }

    protected function generateDeterministicVisaNo(?string $passportNo, ?string $workerName, int $rowIndex): string
    {
        $base = ($passportNo ?? '') . ($workerName ?? '') . $rowIndex;
        $hash = substr(md5($base), 0, 8);
        return 'AUTO-' . strtoupper($hash) . '-' . $rowIndex;
    }

    protected function findOrCreateWorker($name, $passportNo, $sponsorName)
    {
        $worker = null;

        if ($passportNo) {
            $worker = Laborer::where('passport_number', $passportNo)->first();
        }

        if (!$worker && $name) {
            $worker = Laborer::where('name_ar', $name)
                ->orWhere('name_en', $name)
                ->first();
        }

        if (!$worker && ($name || $passportNo)) {
            $agent = $this->findOrCreateAgent($sponsorName);

            if (!$passportNo) {
                $passportNo = 'PASS-' . time() . '-' . rand(1000, 9999);
                while (Laborer::where('passport_number', $passportNo)->exists()) {
                    $passportNo = 'PASS-' . time() . '-' . rand(1000, 9999);
                }
            }

            $fallbackName = $name ?: ('Worker ' . time());

            $worker = Laborer::create([
                'name_ar' => $fallbackName,
                'name_en' => $fallbackName,
                'passport_number' => $passportNo,
                'agent_id' => $agent?->id ?: Agent::first()?->id ?: 1,
                'country_id' => $this->defaultCountry?->id ?: 1,
                'nationality_id' => $this->defaultNationality?->id ?: 1,
                'profession_id' => $this->defaultProfession?->id ?: 1,
                'monthly_salary_amount' => 0,
                'monthly_salary_currency_id' => $this->defaultCurrency?->id ?: 1,
                'is_available' => false,
            ]);
        }

        return $worker;
    }

    protected function findOrCreateClient($name, $idNumber)
    {
        if (empty($name)) return null;

        $client = null;

        if ($idNumber) {
            $client = Client::where('national_id', $idNumber)->first();
        }

        if (!$client) {
            $client = Client::where('name_ar', $name)
                ->orWhere('name_en', $name)
                ->first();
        }

        if (!$client) {
            $client = Client::create([
                'name_ar' => $name,
                'name_en' => $name,
                'national_id' => $idNumber ?: ('ID-' . time()),
                'mobile' => '0000000000',
                'birth_date' => now()->subYears(25),
                'marital_status' => 'single',
                'classification' => 'new',
            ]);
        }

        return $client;
    }

    protected function findOrCreateAgent($name)
    {
        if (empty($name)) {
            return Agent::first();
        }

        $agent = Agent::where('name_ar', $name)
            ->orWhere('name_en', $name)
            ->first();

        if (!$agent) {
            $agent = Agent::create([
                'code' => 'AGT-' . time(),
                'name_ar' => $name,
                'name_en' => $name,
                'country_id' => $this->defaultCountry?->id,
            ]);
        }

        return $agent;
    }

    protected function findOrCreateBranch($name)
    {
        if (empty($name)) {
            return Branch::active()->first() ?? Branch::first();
        }

        $branch = Branch::where('name', $name)->first();

        if (!$branch) {
            $code = 'BR-' . time() . '-' . rand(1000, 9999);
            while (Branch::where('code', $code)->exists()) {
                $code = 'BR-' . time() . '-' . rand(1000, 9999);
            }

            $branch = Branch::create([
                'name' => $name,
                'code' => $code,
                'status' => 'active',
            ]);
        }

        return $branch;
    }

    protected function parseDate($date): ?Carbon
    {
        if (empty($date)) return null;

        try {
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }

            if ($date instanceof \DateTime) {
                return Carbon::instance($date);
            }

            if (is_string($date)) {
                $s = trim($date);

                // لو النص فيه تاريخ داخل جملة مثل: "وصول يوم 25-12-2025 ..."
                if (preg_match('/(\d{4}-\d{2}-\d{2})/', $s, $m)) {
                    return Carbon::parse($m[1]);
                }
                if (preg_match('/(\d{2}-\d{2}-\d{4})/', $s, $m)) {
                    return Carbon::createFromFormat('d-m-Y', $m[1]);
                }

                return Carbon::parse($s);
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function mapStatus($code)
    {
        if (empty($code)) return 'new';

        if (is_string($code)) {
            $code = trim($code);
            $allowed = [
                'new', 'foreign_embassy_approval', 'visa_issued', 'arrived_in_saudi_arabia',
                'rejected', 'cancelled', 'visa_cancelled', 'outside_kingdom', 'processing',
                'external_sending_office_approval', 'accepted_by_external_sending_office',
                'foreign_labor_ministry_approval', 'accepted_by_foreign_labor_ministry',
                'sent_to_saudi_embassy',
            ];
            if (in_array($code, $allowed, true)) return $code;
        }

        $statusMap = [
            1 => 'new',
            2 => 'foreign_embassy_approval',
            3 => 'external_sending_office_approval',
            4 => 'accepted_by_external_sending_office',
            5 => 'foreign_labor_ministry_approval',
            6 => 'accepted_by_foreign_labor_ministry',
            7 => 'sent_to_saudi_embassy',
            8 => 'visa_issued',
            9 => 'arrived_in_saudi_arabia',
            10 => 'rejected',
            11 => 'cancelled',
            12 => 'visa_cancelled',
            13 => 'outside_kingdom',
            14 => 'processing',
        ];

        return $statusMap[(int) $code] ?? 'new';
    }

    /**
     * IMPORTANT:
     * In your excel file the value is "3" (paid) in a long heading column.
     * This function MUST map:
     * 0/1 => unpaid
     * 2   => partial
     * 3   => paid
     * also Arabic strings.
     */
    protected function mapPaymentStatus($raw): ?string
    {
        if ($raw === null) return null;

        $v = trim((string) $raw);
        if ($v === '') return null;

        // Normalize Arabic/English
        $normalized = mb_strtolower($v, 'UTF-8');
        $normalized = str_replace(['_', '-', '  ', "\t", "\n", "\r"], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Numeric-like
        if (is_numeric($normalized)) {
            $n = (int) $normalized;
            return match ($n) {
                2 => 'partial',
                3 => 'paid',
                0, 1 => 'unpaid',
                default => null,
            };
        }

        // Arabic keywords
        if (str_contains($normalized, 'مدفوع') || str_contains($normalized, 'تم الدفع')) return 'paid';
        if (str_contains($normalized, 'جزئي') || str_contains($normalized, 'جزء')) return 'partial';
        if (str_contains($normalized, 'غير مدفوع') || str_contains($normalized, 'غيرمدفوع')) return 'unpaid';

        // English keywords
        if (str_contains($normalized, 'paid')) return 'paid';
        if (str_contains($normalized, 'partial')) return 'partial';
        if (str_contains($normalized, 'unpaid')) return 'unpaid';

        return null;
    }

    /**
     * Always set payment_status to 'paid' regardless of Excel value.
     * Update ALL columns if they exist (no elseif).
     */
    protected function applyPaymentStatus(array $contractData, ?string $paymentStatus): array
    {
        // Always set to 'paid' regardless of input
        $paymentStatus = 'paid';

        if ($this->hasPaymentStatus) {
            $contractData['payment_status'] = $paymentStatus;
        }

        if ($this->hasPaymentStatusCode) {
            $contractData['payment_status_code'] = 3; // 3 = paid
        }

        if ($this->hasIsPaid) {
            $contractData['is_paid'] = true;
        }

        if ($this->hasPaidAt) {
            $contractData['paid_at'] = now();
        }

        return $contractData;
    }

    protected function normalizeVisaNo(?string $visaNo): ?string
    {
        if (empty($visaNo)) return null;

        $normalized = trim((string) $visaNo);
        $normalized = preg_replace('/\s+/', '', $normalized);

        // Excel might read big numbers as float => convert safely
        if (is_numeric($normalized)) {
            $normalized = (string) (int) (float) $normalized;
        }

        return $normalized !== '' ? $normalized : null;
    }

    protected function addError(int $rowIndex, string $reason): void
    {
        $this->errors[] = "Row {$rowIndex}: {$reason}";
    }

    protected function debugLog(int $rowNumber, $paymentRaw, ?string $paymentMapped, array $contractData): void
    {
        if (!env('IMPORT_DEBUG', false)) return;
        if ($this->debugLoggedRows >= 5) return;

        $this->debugLoggedRows++;

        $fields = [
            'payment_status' => $contractData['payment_status'] ?? null,
            'payment_status_code' => $contractData['payment_status_code'] ?? null,
            'is_paid' => $contractData['is_paid'] ?? null,
            'paid_at' => $contractData['paid_at'] ?? null,
        ];

        Log::debug('IMPORT PAYMENT DEBUG', [
            'row' => $rowNumber,
            'raw' => $paymentRaw,
            'mapped' => $paymentMapped,
            'persist_fields' => $fields,
        ]);
    }

    // ===================== Public getters =====================

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
