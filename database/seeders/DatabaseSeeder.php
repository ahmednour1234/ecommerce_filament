<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            RolePermissionSeeder::class, // Seed permissions for all resources
            \Database\Seeders\HR\HrPermissionsSeeder::class, // HR module permissions
            \Database\Seeders\HR\HrTranslationsSeeder::class, // HR module translations
            \Database\Seeders\HR\HrLoansTranslationsSeeder::class, // HR Loans module translations
            \Database\Seeders\HR\HrLookupsSeeder::class, // HR default lookups
            \Database\Seeders\MainCore\MainCoreSeeder::class,
            \Database\Seeders\Catalog\CatalogSeeder::class, // Must be after MainCore (needs Currency)
            \Database\Seeders\Accounting\AccountingSeeder::class, // Must be after MainCore (needs Branch, CostCenter)
            \Database\Seeders\Accounting\BankGuaranteePermissionsSeeder::class, // Bank Guarantee permissions
            \Database\Seeders\Sales\SalesSeeder::class, // Must be after Catalog and Accounting
            \Database\Seeders\Finance\FinancePermissionsSeeder::class, // Finance module permissions
            \Database\Seeders\Finance\FinanceTypesSeeder::class, // Finance types data
            \Database\Seeders\Finance\FinanceTranslationsSeeder::class, // Finance translations
            \Database\Seeders\MainCore\CountriesSeeder::class,
            \Database\Seeders\ClientsPermissionsSeeder::class, // Clients module permissions
            \Database\Seeders\ClientsTranslationsSeeder::class, // Clients module translations
            SuperAdminSeeder::class, // Create super_admin role with ALL permissions (must be last)
        ]);

        // Run payment transactions and shipments after sales data exists
        $this->call([
            \Database\Seeders\MainCore\PaymentTransactionSeeder::class,
            \Database\Seeders\MainCore\ShipmentSeeder::class,
        ]);

        // Only create test user if it doesn't exist
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
