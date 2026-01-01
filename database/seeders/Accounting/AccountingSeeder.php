<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,
            Level5AccountsArabicSeeder::class, // Level 5 accounts with Arabic names (must be after AccountSeeder)
            JournalSeeder::class,
            FiscalYearSeeder::class, // Must be after AccountSeeder
            AccountingPermissionsSeeder::class, // Permissions for accounting module
            AccountingTranslationsSeeder::class, // Translations for accounting module
            JournalEntrySeeder::class,
            JournalEntryLineSeeder::class,
            VoucherSeeder::class,
        ]);
    }
}

