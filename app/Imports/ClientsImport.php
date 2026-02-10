<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientsImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $addedColumns = [];

    public function collection(Collection $rows)
    {
        $tableName = 'clients';
        $existingColumns = Schema::getColumnListing($tableName);
        
        foreach ($rows as $index => $row) {
            $rowArray = is_array($row) ? $row : $row->toArray();
            $columnsToAdd = [];
            
            foreach ($rowArray as $key => $value) {
                if ($value === null || $value === '') continue;
                
                $columnName = $this->sanitizeColumnName($key);
                
                if (!in_array($columnName, $existingColumns) && !in_array($columnName, $this->addedColumns)) {
                    $columnsToAdd[$columnName] = $value;
                }
            }
            
            if (!empty($columnsToAdd)) {
                $this->addColumnsToTable($tableName, $columnsToAdd);
                $this->addedColumns = array_merge($this->addedColumns, array_keys($columnsToAdd));
                $existingColumns = Schema::getColumnListing($tableName);
            }
            
            try {
                $data = $this->mapRowData($rowArray, $existingColumns);
                
                if (!empty($data['national_id']) || !empty($data['name_ar'])) {
                    if (!empty($data['national_id'])) {
                        Client::updateOrCreate(
                            ['national_id' => $data['national_id']],
                            $data
                        );
                    } else {
                        Client::create($data);
                    }
                }
            } catch (\Exception $e) {
                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    protected function mapRowData(array $row, array $existingColumns): array
    {
        $mapping = [
            'name_ar' => ['name_ar', 'name_arabic', 'arabic_name', 'الاسم', 'اسم'],
            'name_en' => ['name_en', 'name_english', 'english_name', 'name'],
            'national_id' => ['national_id', 'nationalid', 'id_number', 'رقم الهوية', 'هوية'],
            'mobile' => ['mobile', 'phone', 'mobile_number', 'رقم الجوال', 'جوال'],
            'mobile2' => ['mobile2', 'mobile_2', 'phone2', 'phone_2', 'رقم الجوال 2'],
            'email' => ['email', 'e_mail', 'البريد الإلكتروني'],
            'birth_date' => ['birth_date', 'birthdate', 'date_of_birth', 'تاريخ الميلاد'],
            'marital_status' => ['marital_status', 'maritalstatus', 'الحالة الاجتماعية'],
            'classification' => ['classification', 'class', 'التصنيف'],
            'building_no' => ['building_no', 'building_number', 'رقم المبنى'],
            'street_name' => ['street_name', 'street', 'اسم الشارع'],
            'city_name' => ['city_name', 'city', 'المدينة', 'مدينه'],
            'district_name' => ['district_name', 'district', 'الحي'],
            'postal_code' => ['postal_code', 'postalcode', 'الرمز البريدي'],
            'additional_no' => ['additional_no', 'additional_number', 'الرقم الإضافي'],
            'unit_no' => ['unit_no', 'unit_number', 'رقم الوحدة'],
            'building_no_en' => ['building_no_en', 'building_number_en'],
            'street_name_en' => ['street_name_en', 'street_en'],
            'city_name_en' => ['city_name_en', 'city_en'],
            'district_name_en' => ['district_name_en', 'district_en'],
            'unit_no_en' => ['unit_no_en', 'unit_number_en'],
            'full_address_ar' => ['full_address_ar', 'address_ar', 'العنوان الكامل'],
            'full_address_en' => ['full_address_en', 'address_en', 'full_address'],
            'housing_type' => ['housing_type', 'housing', 'نوع السكن'],
            'source' => ['source', 'المصدر'],
            'office_referral' => ['office_referral', 'referral', 'الإحالة'],
        ];

        $data = [];
        
        foreach ($mapping as $dbColumn => $possibleKeys) {
            foreach ($possibleKeys as $key) {
                $sanitizedKey = $this->sanitizeColumnName($key);
                if (isset($row[$key]) || isset($row[$sanitizedKey])) {
                    $value = $row[$key] ?? $row[$sanitizedKey] ?? null;
                    if (!empty($value)) {
                        $data[$dbColumn] = $value;
                        break;
                    }
                }
            }
        }
        
        foreach ($row as $key => $value) {
            if (!empty($value)) {
                $columnName = $this->sanitizeColumnName($key);
                if (in_array($columnName, $existingColumns) && !isset($data[$columnName])) {
                    $data[$columnName] = $value;
                }
            }
        }
        
        if (isset($data['birth_date']) && is_string($data['birth_date'])) {
            try {
                $data['birth_date'] = \Carbon\Carbon::parse($data['birth_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                unset($data['birth_date']);
            }
        }
        
        return $data;
    }

    protected function sanitizeColumnName(string $name): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $name));
    }

    protected function addColumnsToTable(string $tableName, array $columns): void
    {
        foreach ($columns as $columnName => $sampleValue) {
            if (!Schema::hasColumn($tableName, $columnName)) {
                $type = $this->determineColumnType($sampleValue);
                
                DB::statement("ALTER TABLE {$tableName} ADD COLUMN `{$columnName}` {$type} NULL");
            }
        }
    }

    protected function determineColumnType($value): string
    {
        if (is_numeric($value)) {
            return 'VARCHAR(255)';
        }
        if (is_string($value) && strlen($value) > 255) {
            return 'TEXT';
        }
        return 'VARCHAR(255)';
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getAddedColumns(): array
    {
        return $this->addedColumns;
    }
}
