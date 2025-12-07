<?php

namespace Database\Seeders\Sales;

use App\Models\MainCore\Currency;
use App\Models\Sales\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            $defaultCurrency = Currency::first();
        }

        $customers = [
            [
                'code' => 'CUST-001',
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
                'currency_id' => $defaultCurrency?->id,
                'credit_limit' => 10000.00,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-002',
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1234567891',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'country' => 'USA',
                'postal_code' => '90001',
                'currency_id' => $defaultCurrency?->id,
                'credit_limit' => 5000.00,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-003',
                'name' => 'ABC Corporation',
                'email' => 'contact@abccorp.com',
                'phone' => '+1234567892',
                'address' => '789 Business Park',
                'city' => 'Chicago',
                'state' => 'IL',
                'country' => 'USA',
                'postal_code' => '60601',
                'currency_id' => $defaultCurrency?->id,
                'credit_limit' => 50000.00,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['code' => $customer['code']],
                $customer
            );
        }
    }
}

