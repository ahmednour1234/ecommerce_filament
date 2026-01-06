<?php

namespace Database\Seeders\HR;

use App\Models\HR\Employee;
use App\Models\HR\Department;
use App\Models\HR\Position;
use App\Models\HR\BloodType;
use App\Models\HR\IdentityType;
use App\Models\HR\Bank;
use App\Models\MainCore\Branch;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample employees for testing.
     */
    public function run(): void
    {
        $this->command->info('Creating sample employees...');

        // Get sample data
        $mainBranch = Branch::where('code', 'MAIN')->first();
        $department = Department::first();
        $position = Position::where('department_id', $department?->id)->first();
        $bloodType = BloodType::first();
        $identityType = IdentityType::first();
        $bank = Bank::first();

        if (!$mainBranch || !$department || !$position) {
            $this->command->warn('Required data (branch, department, position) not found. Skipping employee seeder.');
            return;
        }

        $employees = [
            [
                'employee_number' => 'EMP001',
                'first_name' => 'Ahmed',
                'last_name' => 'Ali',
                'email' => 'ahmed.ali@company.com',
                'phone' => '+1234567890',
                'gender' => 'male',
                'birth_date' => '1990-01-15',
                'fingerprint_device_id' => 'FP001',
                'hire_date' => '2020-01-01',
                'branch_id' => $mainBranch->id,
                'department_id' => $department->id,
                'position_id' => $position->id,
                'basic_salary' => 5000.00,
                'address' => '123 Main Street',
                'city' => 'City',
                'country' => 'Country',
                'identity_type_id' => $identityType?->id,
                'identity_number' => 'ID123456',
                'blood_type_id' => $bloodType?->id,
                'emergency_contact_name' => 'Mohammed Ali',
                'emergency_contact_phone' => '+1234567891',
                'bank_id' => $bank?->id,
                'bank_account_number' => 'ACC123456',
                'iban' => 'SA1234567890123456789012',
                'status' => 'active',
            ],
            [
                'employee_number' => 'EMP002',
                'first_name' => 'Fatima',
                'last_name' => 'Hassan',
                'email' => 'fatima.hassan@company.com',
                'phone' => '+1234567892',
                'gender' => 'female',
                'birth_date' => '1992-05-20',
                'fingerprint_device_id' => 'FP002',
                'hire_date' => '2021-03-15',
                'branch_id' => $mainBranch->id,
                'department_id' => $department->id,
                'position_id' => $position->id,
                'basic_salary' => 4500.00,
                'address' => '456 Second Avenue',
                'city' => 'City',
                'country' => 'Country',
                'identity_type_id' => $identityType?->id,
                'identity_number' => 'ID789012',
                'blood_type_id' => $bloodType?->id,
                'emergency_contact_name' => 'Hassan Ali',
                'emergency_contact_phone' => '+1234567893',
                'bank_id' => $bank?->id,
                'bank_account_number' => 'ACC789012',
                'iban' => 'SA9876543210987654321098',
                'status' => 'active',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(
                ['employee_number' => $employee['employee_number']],
                $employee
            );
        }

        $this->command->info('✓ Sample employees created: ' . count($employees));
    }
}

