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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class RecruitmentContractsImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedCount = 0;
    protected $defaultCountry;
    protected $defaultCurrency;
    protected $defaultNationality;
    protected $defaultProfession;
    protected $defaultUserId;
    protected $firstRowProcessed = false;

    public function __construct()
    {
        $this->defaultCountry = Country::where('is_active', true)->first();
        $this->defaultCurrency = Currency::first();
        $this->defaultNationality = Nationality::where('is_active', true)->first() ?? Nationality::first();
        $this->defaultProfession = Profession::where('is_active', true)->first() ?? Profession::first();
        $this->defaultUserId = Auth::check() ? Auth::id() : config('app.default_user_id', 1);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $rowArray = is_array($row) ? $row : $row->toArray();

                if (!$this->firstRowProcessed && env('IMPORT_DEBUG', false)) {
                    $this->logNormalizedHeadings($rowArray);
                    $this->firstRowProcessed = true;
                }

                $hasData = false;
                foreach ($rowArray as $value) {
                    if (!empty(trim($value ?? ''))) {
                        $hasData = true;
                        break;
                    }
                }

                if (!$hasData) {
                    $this->skippedCount++;
                    continue;
                }

                $workerName = $this->getValue($rowArray, ['name_of_the_worker', 'worker_name', 'name', 'الاسم', 'اسم العامل']);
                $passportNo = $this->getValue($rowArray, ['passport_no', 'passport_number', 'passport', 'رقم الجواز']);
                $clientName = $this->getValue($rowArray, ['client_name', 'client', 'العميل', 'اسم العميل']);
                $sponsorName = $this->getValue($rowArray, ['sponsor_name', 'sponsor', 'الكفيل', 'اسم الكفيل']);
                $branchName = $this->getValue($rowArray, ['branch_name', 'branch', 'الفرع', 'اسم الفرع']);
                $visaNo = $this->getValue($rowArray, ['visa_no', 'visa_number', 'visa', 'رقم التأشيرة']);
                $idNumber = $this->getValue($rowArray, ['id_number', 'id', 'national_id', 'رقم الهوية']);
                $note = $this->getValue($rowArray, ['note', 'notes', 'ملاحظات', 'ملاحظة']);
                $arrivalDate = $this->getValue($rowArray, ['arrival_date', 'arrival', 'تاريخ الوصول']);
                $issueDate = $this->getValue($rowArray, ['issue_date', 'issue', 'تاريخ الإصدار']);
                $statusCode = $this->getValue($rowArray, ['status_code', 'status', 'الحالة']);
                $paymentStatusCode = $this->getValue($rowArray, ['payment_status_code', 'payment_status', 'حالة الدفع', 'payment']);
                $airportName = $this->getValue($rowArray, ['name_of_the_airport', 'airport', 'اسم المطار']);

                $workerName = $workerName ? trim($workerName) : null;
                $passportNo = $passportNo ? trim($passportNo) : null;

                if (empty($workerName) && empty($passportNo)) {
                    $this->addError($index + 2, 'missing worker identity');
                    $this->skippedCount++;
                    continue;
                }

                $worker = $this->findOrCreateWorker($workerName, $passportNo, $sponsorName);
                $client = $this->findOrCreateClient($clientName, $idNumber);
                $agent = $this->findOrCreateAgent($sponsorName);
                $branch = $this->findOrCreateBranch($branchName);

                if (!$worker) {
                    $this->addError($index + 2, 'could not create worker');
                    $this->skippedCount++;
                    continue;
                }

                $arrivalCountryId = $this->mapCountryIdByName($airportName);
                $departureCountryId = $this->mapCountryIdByName($airportName);
                $receivingStationId = $this->mapReceivingStationIdByName($airportName);

                $visaNoValue = $visaNo ? trim($visaNo) : null;
                if (empty($visaNoValue)) {
                    $visaNoValue = $this->generateDeterministicVisaNo($passportNo, $workerName, $index);
                }

                $contractData = [
                    'client_id' => $client?->id,
                    'branch_id' => $branch?->id,
                    'worker_id' => $worker->id,
                    'visa_no' => $visaNoValue,
                    'notes' => $note ? trim($note) : null,
                    'status' => $this->mapStatus($statusCode),
                    'payment_status' => $this->mapPaymentStatus($paymentStatusCode),
                    'arrival_country_id' => $arrivalCountryId,
                    'departure_country_id' => $departureCountryId,
                    'receiving_station_id' => $receivingStationId,
                    'gregorian_request_date' => $arrivalDate ? $this->parseDate($arrivalDate) : now(),
                    'visa_date' => $issueDate ? $this->parseDate($issueDate) : null,
                    'created_by' => $this->defaultUserId,
                ];

                try {
                    RecruitmentContract::updateOrCreate(
                        ['visa_no' => $visaNoValue],
                        $contractData
                    );

                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->addError($index + 2, 'db constraint failed: ' . $e->getMessage());
                    $this->skippedCount++;
                }
            } catch (\Exception $e) {
                $this->addError($index + 2, $e->getMessage());
                $this->skippedCount++;
            }
        }
    }

    protected function normalizeKey(string $key): string
    {
        $normalized = mb_strtolower($key, 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}_]+/u', '_', $normalized);
        $normalized = preg_replace('/_+/', '_', $normalized);
        $normalized = trim($normalized, '_');
        return $normalized;
    }

    protected function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);

            if (isset($row[$key])) {
                $value = trim($row[$key]);
                if ($value !== '' && $value !== null) {
                    return $value;
                }
            }

            $sanitizedKey = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $key));

            if (isset($row[$sanitizedKey])) {
                $value = trim($row[$sanitizedKey]);
                if ($value !== '' && $value !== null) {
                    return $value;
                }
            }

            foreach ($row as $rowKey => $rowValue) {
                $normalizedRowKey = $this->normalizeKey($rowKey);

                if ($normalizedRowKey === $normalizedKey) {
                    $value = trim($rowValue);
                    if ($value !== '' && $value !== null) {
                        return $value;
                    }
                }

                if (str_starts_with($normalizedRowKey, $normalizedKey) ||
                    str_contains($normalizedRowKey, $normalizedKey)) {
                    $value = trim($rowValue);
                    if ($value !== '' && $value !== null) {
                        return $value;
                    }
                }
            }
        }
        return null;
    }

    protected function mapCountryIdByName(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

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
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function mapReceivingStationIdByName(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        try {
            $stationClass = 'App\Models\Recruitment\ReceivingStation';
            if (class_exists($stationClass)) {
                $station = $stationClass::where('name', $name)
                    ->orWhere('name_ar', $name)
                    ->orWhere('name_en', $name)
                    ->first();

                return $station?->id;
            }
        } catch (\Exception $e) {
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

            $workerData = [
                'name_ar' => $name ?: 'Worker ' . time(),
                'name_en' => $name ?: 'Worker ' . time(),
                'passport_number' => $passportNo,
                'agent_id' => $agent?->id ?: Agent::first()?->id ?: 1,
                'country_id' => $this->defaultCountry?->id ?: 1,
                'nationality_id' => $this->defaultNationality?->id ?: 1,
                'profession_id' => $this->defaultProfession?->id ?: 1,
                'monthly_salary_amount' => 0,
                'monthly_salary_currency_id' => $this->defaultCurrency?->id ?: 1,
                'is_available' => false,
            ];

            $worker = Laborer::create($workerData);
        }

        return $worker;
    }

    protected function findOrCreateClient($name, $idNumber)
    {
        if (empty($name)) {
            return null;
        }

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
                'national_id' => $idNumber ?: 'ID-' . time(),
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
            return Branch::active()->first();
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
        if (empty($date)) {
            return null;
        }

        try {
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }

            if (is_string($date)) {
                return Carbon::parse($date);
            }

            if ($date instanceof \DateTime) {
                return Carbon::instance($date);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function mapStatus($code)
    {
        if (empty($code)) {
            return 'new';
        }

        if (is_string($code)) {
            $code = trim($code);
            if (in_array($code, ['new', 'foreign_embassy_approval', 'visa_issued', 'arrived_in_saudi_arabia', 'rejected', 'cancelled', 'visa_cancelled', 'outside_kingdom', 'processing'])) {
                return $code;
            }
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

        return $statusMap[(int)$code] ?? 'new';
    }

    protected function mapPaymentStatus($code)
    {
        if (empty($code)) {
            return null;
        }

        if (is_string($code)) {
            $code = strtolower(trim($code));
            if ($code === 'unpaid' || $code === '1') {
                return 'unpaid';
            }
            if ($code === 'partial' || $code === '2') {
                return 'partial';
            }
            if ($code === 'paid' || $code === '3') {
                return 'paid';
            }
        }

        $paymentStatusMap = [
            1 => 'unpaid',
            2 => 'partial',
            3 => 'paid',
        ];

        return $paymentStatusMap[(int)$code] ?? null;
    }

    protected function addError(int $rowIndex, string $reason): void
    {
        $this->errors[] = "Row {$rowIndex}: {$reason}";
    }

    protected function logNormalizedHeadings(array $rowArray): void
    {
        $normalized = [];
        foreach ($rowArray as $key => $value) {
            $normalized[$key] = $this->normalizeKey($key);
        }
        Log::debug('Import normalized headings', ['headings' => $normalized, 'original' => array_keys($rowArray)]);
    }

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
