<?php

namespace Database\Seeders\HR;

use App\Models\HR\SalaryComponent;
use Illuminate\Database\Seeder;

class SalaryComponentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating salary components...');

        $components = [
            [
                'name' => 'Base Salary',
                'code' => 'base_salary',
                'type' => 'earning',
                'is_fixed' => true,
                'taxable' => true,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Overtime',
                'code' => 'overtime',
                'type' => 'earning',
                'is_fixed' => false,
                'taxable' => true,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Transport Allowance',
                'code' => 'transport_allowance',
                'type' => 'earning',
                'is_fixed' => true,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Housing Allowance',
                'code' => 'housing_allowance',
                'type' => 'earning',
                'is_fixed' => true,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Medical Allowance',
                'code' => 'medical_allowance',
                'type' => 'earning',
                'is_fixed' => true,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Bonus',
                'code' => 'bonus',
                'type' => 'earning',
                'is_fixed' => false,
                'taxable' => true,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Loan Installment',
                'code' => 'loan_installment',
                'type' => 'deduction',
                'is_fixed' => false,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Insurance',
                'code' => 'insurance',
                'type' => 'deduction',
                'is_fixed' => true,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Social Security',
                'code' => 'social_security',
                'type' => 'deduction',
                'is_fixed' => true,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax',
                'code' => 'income_tax',
                'type' => 'deduction',
                'is_fixed' => false,
                'taxable' => false,
                'default_amount' => 0,
                'is_active' => true,
            ],
        ];

        $created = 0;
        foreach ($components as $component) {
            SalaryComponent::firstOrCreate(
                ['code' => $component['code']],
                $component
            );
            $created++;
        }

        $this->command->info("✓ Salary components created: {$created}");
    }
}
