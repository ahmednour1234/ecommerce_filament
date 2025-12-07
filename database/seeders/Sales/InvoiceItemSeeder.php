<?php

namespace Database\Seeders\Sales;

use App\Models\Catalog\Product;
use App\Models\Sales\Invoice;
use App\Models\Sales\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $invoice = Invoice::where('invoice_number', 'INV-001')->first();
        $product = Product::first();

        if (!$invoice || !$product) {
            return; // Skip if required dependencies don't exist
        }

        $invoiceItems = [
            [
                'invoice_id' => $invoice->id,
                'product_id' => $product->id,
                'description' => $product->name,
                'quantity' => 2,
                'unit_price' => $product->price,
                'discount' => 0.00,
                'total' => $product->price * 2,
            ],
        ];

        foreach ($invoiceItems as $item) {
            InvoiceItem::updateOrCreate(
                [
                    'invoice_id' => $item['invoice_id'],
                    'product_id' => $item['product_id'],
                ],
                $item
            );
        }
    }
}

