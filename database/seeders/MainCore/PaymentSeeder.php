<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\PaymentProvider;
use App\Models\MainCore\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // مثال: Cash (بدون provider)
        PaymentMethod::updateOrCreate(
            ['code' => 'cash_on_delivery'],
            [
                'provider_id'  => null,
                'name'         => 'Cash on Delivery',
                'fee_fixed'    => 0,
                'fee_percent'  => 0,
                'is_default'   => true,
                'is_active'    => true,
                'display_order'=> 1,
            ]
        );

        // مثال Provider Stripe
        $stripe = PaymentProvider::updateOrCreate(
            ['code' => 'stripe'],
            [
                'name'    => 'Stripe',
                'driver'  => 'App\\Payments\\StripeGateway',
                'config'  => [
                    'public_key' => 'pk_test_xxx',
                    'secret_key' => 'sk_test_xxx',
                    'mode'       => 'test',
                ],
                'is_active' => false,
            ]
        );

        PaymentMethod::updateOrCreate(
            ['code' => 'card'],
            [
                'provider_id'   => $stripe->id,
                'name'          => 'Credit Card',
                'fee_fixed'     => 0,
                'fee_percent'   => 0,
                'is_default'    => false,
                'is_active'     => true,
                'display_order' => 2,
            ]
        );
    }
}
