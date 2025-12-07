<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Currency;
use App\Models\MainCore\PaymentMethod;
use App\Models\MainCore\PaymentProvider;
use App\Models\MainCore\PaymentTransaction;
use App\Models\Sales\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            $defaultCurrency = Currency::first();
        }

        $paymentMethod = PaymentMethod::first();
        $paymentProvider = PaymentProvider::first();
        $invoice = Invoice::first();
        $user = User::first();

        if (!$paymentMethod || !$paymentProvider || !$invoice || !$user) {
            return; // Skip if required dependencies don't exist
        }

        $transactions = [
            [
                'payable_type' => Invoice::class,
                'payable_id' => $invoice->id,
                'user_id' => $user->id,
                'payment_method_id' => $paymentMethod->id,
                'provider_id' => $paymentProvider->id,
                'currency_id' => $defaultCurrency->id,
                'amount' => $invoice->total,
                'status' => 'completed',
                'provider_reference' => 'TXN-' . strtoupper(uniqid()),
                'paid_at' => now()->subDays(7),
            ],
        ];

        foreach ($transactions as $transaction) {
            PaymentTransaction::create($transaction);
        }
    }
}

