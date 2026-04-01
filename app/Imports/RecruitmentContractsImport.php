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
use App\Data\SaudiGovernorates;
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
                $clientNationalId = $this->getValue($rowArray, ['client_national_id', 'client_id_number', 'رقم هوية العميل']);
                $sponsorName = $this->getValue($rowArray, ['sponsor_name', 'sponsor', 'الكفيل', 'اسم الكفيل']);
                $branchName  = $this->getValue($rowArray, ['branch_name', 'branch', 'الفرع', 'اسم الفرع']);
                $visaNo      = $this->getValue($rowArray, ['visa_no', 'visa_number', 'visa', 'رقم التأشيرة']);
                $visaTypeRaw = $this->getValue($rowArray, ['visa_type', 'type', 'نوع التأشيرة', 'نوع_التأشيرة']);
                $visaDate   = $this->getValue($rowArray, ['visa_date', 'visa_date', 'تاريخ التأشيرة']);
                $arrivalCountry = $this->getValue($rowArray, ['arrival_country', 'arrival_country', 'محطة الوصول']);
                $departureCountry = $this->getValue($rowArray, ['departure_country', 'departure_country', 'محطة القدوم']);
                $receivingStation = $this->getValue($rowArray, ['receiving_station', 'receiving_station', 'محطة الاستلام']);
                $professionName = $this->getValue($rowArray, ['profession', 'profession', 'المهنة']);
                $nationalityName = $this->getValue($rowArray, ['nationality', 'nationality', 'الجنسية']);
                $gender = $this->getValue($rowArray, ['gender', 'gender', 'الجنس']);
                $experience = $this->getValue($rowArray, ['experience', 'experience', 'الخبرة']);
                $religion = $this->getValue($rowArray, ['religion', 'religion', 'الدين']);
                $workplaceAr = $this->getValue($rowArray, ['workplace_ar', 'workplace_ar', 'مكان العمل (عربي)']);
                $workplaceEn = $this->getValue($rowArray, ['workplace_en', 'workplace_en', 'مكان العمل (إنجليزي)']);
                $monthlySalary = $this->getValue($rowArray, ['monthly_salary', 'monthly_salary', 'الراتب الشهري']);
                $gregorianRequestDate = $this->getValue($rowArray, ['gregorian_request_date', 'gregorian_request_date', 'تاريخ الطلب']);
                $hijriRequestDate = $this->getValue($rowArray, ['hijri_request_date', 'hijri_request_date', 'التاريخ الهجري']);
                $statusRaw = $this->getValue($rowArray, ['status', 'status', 'الحالة']);
                $paymentStatusRaw = $this->getValue($rowArray, [
                    'payment_status',
                    'payment_status_code',
                    'payment status',
                    'حالة الدفع',
                    'حالة_الدفع',
                    'payment',
                ]);
                $musanedContractDate = $this->getValue($rowArray, ['musaned_contract_date', 'musaned_contract_date', 'تاريخ عقد مساند']);
                $arrivalDateRaw = $this->getValue($rowArray, ['arrival_date', 'تاريخ الوصول']);

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

                $client = $this->findOrCreateClient($clientName, $clientNationalId ?: null);
                $this->findOrCreateAgent($sponsorName); // ensures sponsor exists if needed
                $branch = $this->findOrCreateBranch($branchName);

                // ===== Visa no normalization =====
                $visaNoValue = $this->normalizeVisaNo($visaNo);
                if (empty($visaNoValue)) {
                    $visaNoValue = $this->generateDeterministicVisaNo($passportNo, $workerName, $index);
                }

                // ===== Map payment status =====
                $paymentStatus = $this->mapPaymentStatus($paymentStatusRaw);

                // ===== Map visa type =====
                $visaType = $this->mapVisaType($visaTypeRaw);

                // ===== Map status =====
                $status = $this->mapStatus($statusRaw);

                // ===== Find relations =====
                $profession = $this->findProfession($professionName);
                $nationality = $this->findNationality($nationalityName);
                // arrival/departure store Arabic city names as strings (not FK integers)
                $arrivalCountryValue  = $arrivalCountry  ? trim((string) $arrivalCountry)  : null;
                $departureCountryValue = $departureCountry ? trim((string) $departureCountry) : null;
                $receivingStationId = $this->mapReceivingStationIdByName($receivingStation);

                // ===== Build contract data =====
                $contractData = [
                    'client_id' => $client?->id,
                    'branch_id' => $branch?->id,
                    'worker_id' => $worker->id,
                    'current_section' => RecruitmentContract::SECTION_COORDINATION,
                    'visa_no' => $visaNoValue,
                    'visa_type' => $visaType,
                    'visa_date' => $visaDate ? $this->parseDate($visaDate) : null,
                    'status' => $status,
                    'arrival_country_id' => $arrivalCountryValue,
                    'departure_country_id' => $departureCountryValue,
                    'receiving_station_id' => $receivingStationId,
                    'profession_id' => $profession?->id,
                    'nationality_id' => $nationality?->id,
                    'gender' => $this->normalizeGender($gender),
                    'experience' => $this->normalizeExperience($experience),
                    'religion' => $religion ? trim((string) $religion) : null,
                    'workplace_ar' => $workplaceAr ? trim((string) $workplaceAr) : null,
                    'workplace_en' => $workplaceEn ? trim((string) $workplaceEn) : null,
                    'monthly_salary' => $this->parseDecimal($monthlySalary),
                    'gregorian_request_date' => $gregorianRequestDate ? ($this->parseDate($gregorianRequestDate) ?? now()) : now(),
                    'hijri_request_date' => $hijriRequestDate ? trim((string) $hijriRequestDate) : null,
                    'musaned_contract_date' => $musanedContractDate ? $this->parseDate($musanedContractDate) : null,
                    'arrival_date' => $arrivalDateRaw ? $this->parseArrivalDate($arrivalDateRaw) : null,
                    'created_by' => $this->defaultUserId,
                ];

                // ===== Apply payment status (if paid, set as paid without checking) =====
                $contractData = $this->applyPaymentStatus($contractData, $paymentStatus, true);

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

    protected function mapReceivingStationIdByName(?string $name): ?string
    {
        if (empty($name)) return null;

        try {
            $name = trim($name);
            $governorates = SaudiGovernorates::all();

            // Check if the name exists in the governorates list
            if (isset($governorates[$name])) {
                return $name;
            }

            // Try case-insensitive match
            foreach ($governorates as $key => $value) {
                if (mb_strtolower($key, 'UTF-8') === mb_strtolower($name, 'UTF-8')) {
                    return $key;
                }
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

    protected function parseArrivalDate($value): ?string
    {
        if (empty($value)) return null;

        $s = trim((string) $value);

        // Format: "وصول يوم 06-04-2026 الساعة 15:35 مساءً مطار ..."
        // Extract DD-MM-YYYY
        if (preg_match('/(\d{1,2}-\d{1,2}-\d{4})/', $s, $m)) {
            try {
                return Carbon::createFromFormat('d-m-Y', $m[1])->toDateString();
            } catch (\Throwable $e) {}
        }

        // Fallback to generic parseDate
        $parsed = $this->parseDate($value);
        return $parsed?->toDateString();
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

        // Convert to integer
        $codeInt = (int) $code;

        // Validate range
        if ($codeInt < 1 || $codeInt > 15) {
            return 'new';
        }

        $statusMap = [
            1  => 'new',                               // جديد
            2  => 'foreign_embassy_approval',          // موافقة السفارة الأجنبية
            3  => 'external_office_approval',          // موافقة المكتب الخارجي
            4  => 'contract_accepted_external_office', // قبول العقد من مكتب الخارجي
            5  => 'waiting_approval',                  // انتظار الابروف
            6  => 'contract_accepted_labor_ministry',  // قبول العقد من مكتب العمل الخارجي
            7  => 'sent_to_saudi_embassy',             // إرسال التأشيرة إلى السفارة السعودية
            8  => 'visa_issued',                       // تم التفييز
            9  => 'visa_cancelled',                    // إلغاء التفييز
            10 => 'travel_permit_after_visa_issued',   // تصريح سفر
            11 => 'waiting_flight_booking',            // انتظار حجز تذكرة الطيران
            12 => 'arrival_scheduled',                 // معاد الوصول
            13 => 'received',                          // تم الاستلام
            14 => 'return_during_warranty',            // رجيع خلال فترة الضمان
            15 => 'runaway',                           // هروب
        ];

        return $statusMap[$codeInt] ?? 'new';
    }

    protected function findProfession(?string $name)
    {
        if (empty($name)) return null;

        return Profession::where('name_ar', $name)
            ->orWhere('name_en', $name)
            ->where('is_active', true)
            ->first() ?? $this->defaultProfession;
    }

    protected function findNationality(?string $name)
    {
        if (empty($name)) return null;

        return Nationality::where('name_ar', $name)
            ->orWhere('name_en', $name)
            ->where('is_active', true)
            ->first() ?? $this->defaultNationality;
    }

    protected function normalizeGender(?string $gender): ?string
    {
        if (empty($gender)) return null;

        $normalized = mb_strtolower(trim($gender), 'UTF-8');

        if (in_array($normalized, ['male', 'ذكر', 'm', 'رجل'])) return 'male';
        if (in_array($normalized, ['female', 'أنثى', 'f', 'امرأة'])) return 'female';

        return null;
    }

    protected function normalizeExperience($value): ?string
    {
        if (empty($value)) return 'unspecified';

        $v = mb_strtolower(trim((string) $value), 'UTF-8');

        // Already a valid ENUM value
        if (in_array($v, ['unspecified', 'new', 'ex_worker'])) return $v;

        // Arabic: جديد / بدون خبرة
        if (in_array($v, ['جديد', 'بدون خبرة', 'لا توجد', 'لا يوجد', '0', '0 years', '0 سنوات'])) return 'new';

        // Numeric years string like "4 years", "2 سنوات", "3" etc.
        if (preg_match('/^(\d+)/', $v, $m)) {
            $years = (int) $m[1];
            return $years === 0 ? 'new' : 'ex_worker';
        }

        // Arabic/English "خبرة" or "experienced"
        if (str_contains($v, 'خبرة') || str_contains($v, 'experienc') || str_contains($v, 'ex_worker')) {
            return 'ex_worker';
        }

        return 'unspecified';
    }

    protected function parseDecimal($value): ?float
    {
        if (empty($value)) return null;

        $value = trim((string) $value);
        $value = str_replace([',', ' '], '', $value);

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    protected function mapPaymentStatus($raw): ?string
    {
        if ($raw === null) return 'unpaid';

        // Convert to integer
        $codeInt = (int) $raw;

        // Validate range (1-3)
        if ($codeInt < 1 || $codeInt > 3) {
            return 'unpaid';
        }

        // Map: 1=unpaid, 2=partial, 3=paid
        return match ($codeInt) {
            1 => 'unpaid',   // غير مدفوع
            2 => 'partial',  // جزئي
            3 => 'paid',     // مدفوع
            default => 'unpaid',
        };
    }

    protected function mapVisaType($raw): string
    {
        if ($raw === null || $raw === '') return 'paid';

        $v = trim((string) $raw);
        if ($v === '') return 'paid';

        $normalized = mb_strtolower($v, 'UTF-8');
        $normalized = str_replace(['_', '-', '  ', "\t", "\n", "\r"], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        if (in_array($normalized, ['paid', 'مدفوع', 'paid visa'])) return 'paid';
        if (str_contains($normalized, 'عمالة منزلية') || str_contains($normalized, 'domestic labor') || str_contains($normalized, 'domestic_labor')) return 'domestic_labor';
        if (str_contains($normalized, 'تأهيل شامل') || str_contains($normalized, 'comprehensive qualification') || str_contains($normalized, 'comprehensive_qualification')) return 'comprehensive_qualification';

        return 'paid';
    }

    protected function applyPaymentStatus(array $contractData, ?string $paymentStatus, bool $forcePaid = false): array
    {
        if (!$paymentStatus) {
            $paymentStatus = 'unpaid';
        }

        // If payment_status is "paid" in Excel, set as paid without checking
        if ($paymentStatus === 'paid' || $forcePaid) {
            if ($this->hasPaymentStatus) {
                $contractData['payment_status'] = 'paid';
            }

            if ($this->hasPaymentStatusCode) {
                $contractData['payment_status_code'] = 3;
            }

            if ($this->hasIsPaid) {
                $contractData['is_paid'] = true;
            }

            if ($this->hasPaidAt) {
                $contractData['paid_at'] = now();
            }

            // Set paid_total = total_cost if paid
            if (isset($contractData['total_cost']) && $contractData['total_cost'] > 0) {
                $contractData['paid_total'] = $contractData['total_cost'];
                $contractData['remaining_total'] = 0;
            }
        } else {
            if ($this->hasPaymentStatus) {
                $contractData['payment_status'] = $paymentStatus;
            }

            if ($this->hasPaymentStatusCode) {
                $contractData['payment_status_code'] = match ($paymentStatus) {
                    'paid' => 3,
                    'partial' => 2,
                    default => 1,
                };
            }

            if ($this->hasIsPaid) {
                $contractData['is_paid'] = $paymentStatus === 'paid';
            }

            if ($this->hasPaidAt) {
                $contractData['paid_at'] = $paymentStatus === 'paid' ? now() : null;
            }
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
