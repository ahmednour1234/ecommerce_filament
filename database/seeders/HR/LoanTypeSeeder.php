<?php

namespace Database\Seeders\HR;

use App\Models\HR\LoanType;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Loan Types...');

        $loanTypes = [
            [
                'name_json' => [
                    'ar' => 'قرض شخصي',
                    'en' => 'Personal Loan',
                ],
                'description_json' => [
                    'ar' => 'قرض شخصي للموظفين',
                    'en' => 'Personal loan for employees',
                ],
                'max_amount' => 50000.00,
                'max_installments' => 24,
                'is_active' => true,
            ],
            [
                'name_json' => [
                    'ar' => 'قرض سكني',
                    'en' => 'Housing Loan',
                ],
                'description_json' => [
                    'ar' => 'قرض لشراء أو بناء مسكن',
                    'en' => 'Loan for purchasing or building a house',
                ],
                'max_amount' => 200000.00,
                'max_installments' => 60,
                'is_active' => true,
            ],
            [
                'name_json' => [
                    'ar' => 'قرض سيارة',
                    'en' => 'Car Loan',
                ],
                'description_json' => [
                    'ar' => 'قرض لشراء سيارة',
                    'en' => 'Loan for purchasing a car',
                ],
                'max_amount' => 100000.00,
                'max_installments' => 48,
                'is_active' => true,
            ],
            [
                'name_json' => [
                    'ar' => 'قرض طارئ',
                    'en' => 'Emergency Loan',
                ],
                'description_json' => [
                    'ar' => 'قرض للطوارئ والحالات الطارئة',
                    'en' => 'Loan for emergencies and urgent cases',
                ],
                'max_amount' => 20000.00,
                'max_installments' => 12,
                'is_active' => true,
            ],
            [
                'name_json' => [
                    'ar' => 'قرض تعليمي',
                    'en' => 'Education Loan',
                ],
                'description_json' => [
                    'ar' => 'قرض لتغطية مصاريف التعليم',
                    'en' => 'Loan for covering education expenses',
                ],
                'max_amount' => 30000.00,
                'max_installments' => 36,
                'is_active' => true,
            ],
        ];

        $created = 0;
        foreach ($loanTypes as $loanType) {
            LoanType::updateOrCreate(
                ['name_json' => $loanType['name_json']],
                $loanType
            );
            $created++;
        }

        $this->command->info("✓ Loan types seeded: {$created}");
    }
}
