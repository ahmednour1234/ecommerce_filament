<?php

namespace Database\Seeders\Sales;

use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use App\Models\Sales\Customer;
use App\Models\Sales\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            $defaultCurrency = Currency::first();
        }

        $customer = Customer::first();
        $branch = Branch::first();
        $costCenter = \App\Models\MainCore\CostCenter::first();

        if (!$customer || !$defaultCurrency) {
            return; // Skip if required dependencies don't exist
        }

        $orders = [
            [
                'order_number' => 'ORD-001',
                'order_date' => now()->subDays(10),
                'customer_id' => $customer->id,
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
                'status' => 'completed',
                'subtotal' => 1998.00,
                'tax_amount' => 199.80,
                'discount_amount' => 0.00,
                'total' => 2197.80,
                'currency_id' => $defaultCurrency->id,
                'notes' => 'First order from customer',
            ],
            [
                'order_number' => 'ORD-002',
                'order_date' => now()->subDays(5),
                'customer_id' => $customer->id,
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
                'status' => 'pending',
                'subtotal' => 899.00,
                'tax_amount' => 89.90,
                'discount_amount' => 50.00,
                'total' => 938.90,
                'currency_id' => $defaultCurrency->id,
                'notes' => 'Pending payment',
            ],
        ];

        foreach ($orders as $order) {
            Order::updateOrCreate(
                ['order_number' => $order['order_number']],
                $order
            );
        }
    }
}

