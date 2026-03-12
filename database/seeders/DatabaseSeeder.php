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
            \Database\Seeders\HR\HrRoleSeeder::class, // HR Manager role and permissions
            \Database\Seeders\HR\HrLookupsSeeder::class, // HR default lookups
            \Database\Seeders\MainCore\MainCoreSeeder::class,
            \Database\Seeders\Housing\HousingStatusSeeder::class,
            \Database\Seeders\Housing\HousingPermissionsSeeder::class, // Housing module permissions
            \Database\Seeders\Housing\DriverManagementRoleSeeder::class, // Driver Management role and permissions
            AllTranslationsSeeder::class, // All translations in one unified seeder
            \Database\Seeders\Catalog\CatalogSeeder::class, // Must be after MainCore (needs Currency)
            \Database\Seeders\Accounting\AccountingSeeder::class, // Must be after MainCore (needs Branch, CostCenter)
            \Database\Seeders\Accounting\AccountingPermissionsSeeder::class, // Accounting module permissions
            \Database\Seeders\Accounting\AccountingRoleSeeder::class, // Accounting Manager role and permissions
            \Database\Seeders\Accounting\BankGuaranteePermissionsSeeder::class, // Bank Guarantee permissions
            \Database\Seeders\Sales\SalesSeeder::class, // Must be after Catalog and Accounting
            \Database\Seeders\Finance\FinancePermissionsSeeder::class, // Finance module permissions
            \Database\Seeders\Finance\FinanceSectionRoleSeeder::class, // Finance Section Manager role and permissions
            \Database\Seeders\Finance\FinanceTypesSeeder::class, // Finance types data
            \Database\Seeders\MainCore\CountriesSeeder::class,
            \Database\Seeders\ClientsPermissionsSeeder::class, // Clients module permissions
            \Database\Seeders\Clients\ClientsRoleSeeder::class, // Clients Manager role and permissions
            \Database\Seeders\Recruitment\RecruitmentPermissionsSeeder::class, // Recruitment module permissions
            \Database\Seeders\Recruitment\RecruitmentRoleSeeder::class, // Recruitment Manager role and permissions
            \Database\Seeders\Recruitment\RecruitmentContractPermissionsSeeder::class, // Recruitment Contracts permissions
            \Database\Seeders\Recruitment\RecruitmentContractRoleSeeder::class, // Recruitment Contracts Manager role and permissions
            \Database\Seeders\Recruitment\RecruitmentContractsOnlyRoleSeeder::class,
            \Database\Seeders\Rental\RentalPermissionsSeeder::class, // Rental module permissions
            \Database\Seeders\Rental\RentalRoleSeeder::class, // Rental Section Manager role and permissions
            \Database\Seeders\ContractsPermissionsSeeder::class, // Contracts permissions
            \Database\Seeders\Messaging\MessagingPermissionsSeeder::class, // Messaging module permissions
            \Database\Seeders\Messaging\MessagingRoleSeeder::class, // Messaging Manager role and permissions
            \Database\Seeders\ComplaintPermissionsSeeder::class, // Complaints permissions
            \Database\Seeders\ComplaintRoleSeeder::class, // Complaints Manager role and permissions
            \Database\Seeders\HR\EmployeeCommissionPermissionsSeeder::class, // Employee Commission permissions
            \Database\Seeders\HR\EmployeeCommissionRoleSeeder::class, // Employee Commission Manager role and permissions
            \Database\Seeders\Packages\PackagesPermissionsSeeder::class, // Packages module permissions
            \Database\Seeders\Packages\PackagesRoleSeeder::class, // Packages Manager role and permissions
            \Database\Seeders\Notifications\NotificationsRoleSeeder::class, // Notifications Manager role and permissions
            \Modules\CompanyVisas\Database\Seeders\CompanyVisasPermissionsSeeder::class, // Company Visas permissions
            \Modules\CompanyVisas\Database\Seeders\CompanyVisasRoleSeeder::class, // Company Visas Manager role and permissions
            \Modules\CompanyVisas\Database\Seeders\CompanyVisasTranslationsSeeder::class, // Company Visas translations
            \Modules\ServiceTransfer\Database\Seeders\ServiceTransferPermissionsSeeder::class, // Service Transfer permissions
            \Database\Seeders\ServiceTransfer\ServiceTransferRoleSeeder::class, // Service Transfer Manager role and permissions
            \Modules\ServiceTransfer\Database\Seeders\ServiceTransferTranslationsSeeder::class, // Service Transfer translations
            SuperAdminSeeder::class, // Create super_admin role with ALL permissions (must be last)
            AdditionalUsersSeeder::class, // Create additional users with super_admin role
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
