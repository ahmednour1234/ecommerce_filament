<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,
            JournalSeeder::class,
            JournalEntrySeeder::class,
            JournalEntryLineSeeder::class,
            VoucherSeeder::class,
        ]);
    }
}

