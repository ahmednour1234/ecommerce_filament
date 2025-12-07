<?php

namespace Database\Seeders\Sales;

use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CustomerSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            InvoiceSeeder::class,
            InvoiceItemSeeder::class,
        ]);
    }
}

