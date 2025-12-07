<?php

namespace Database\Seeders\Sales;

use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use App\Models\Sales\Customer;
use App\Models\Sales\Invoice;
use App\Models\Sales\Order;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            $defaultCurrency = Currency::first();
        }

        $customer = Customer::first();
        $order = Order::where('order_number', 'ORD-001')->first();
        $branch = Branch::first();
        $costCenter = \App\Models\MainCore\CostCenter::first();

        if (!$customer || !$defaultCurrency) {
            return; // Skip if required dependencies don't exist
        }

        $invoices = [
            [
                'invoice_number' => 'INV-001',
                'invoice_date' => now()->subDays(8),
                'order_id' => $order?->id,
                'customer_id' => $customer->id,
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
                'status' => 'paid',
                'subtotal' => 1998.00,
                'tax_amount' => 199.80,
                'discount_amount' => 0.00,
                'total' => 2197.80,
                'currency_id' => $defaultCurrency->id,
                'due_date' => now()->subDays(1),
                'paid_at' => now()->subDays(7),
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::updateOrCreate(
                ['invoice_number' => $invoice['invoice_number']],
                $invoice
            );
        }
    }
}

