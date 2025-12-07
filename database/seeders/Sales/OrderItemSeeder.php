<?php

namespace Database\Seeders\Sales;

use App\Models\Catalog\Product;
use App\Models\Sales\Order;
use App\Models\Sales\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::where('order_number', 'ORD-001')->first();
        $product = Product::first();

        if (!$order || !$product) {
            return; // Skip if required dependencies don't exist
        }

        $orderItems = [
            [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => $product->price,
                'discount' => 0.00,
                'total' => $product->price * 2,
            ],
        ];

        foreach ($orderItems as $item) {
            OrderItem::updateOrCreate(
                [
                    'order_id' => $item['order_id'],
                    'product_id' => $item['product_id'],
                ],
                $item
            );
        }
    }
}

