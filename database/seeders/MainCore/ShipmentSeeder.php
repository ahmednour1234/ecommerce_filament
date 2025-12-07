<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Currency;
use App\Models\MainCore\Shipment;
use App\Models\MainCore\ShippingProvider;
use App\Models\Sales\Order;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            $defaultCurrency = Currency::first();
        }

        $shippingProvider = ShippingProvider::first();
        $order = Order::first();

        if (!$shippingProvider || !$order) {
            return; // Skip if required dependencies don't exist
        }

        $shipments = [
            [
                'shippable_type' => Order::class,
                'shippable_id' => $order->id,
                'shipping_provider_id' => $shippingProvider->id,
                'tracking_number' => 'TRACK-' . strtoupper(uniqid()),
                'status' => 'shipped',
                'currency_id' => $defaultCurrency->id,
                'price' => 25.00,
                'meta' => [
                    'estimated_delivery' => now()->addDays(5)->toDateString(),
                ],
            ],
        ];

        foreach ($shipments as $shipment) {
            Shipment::create($shipment);
        }
    }
}

