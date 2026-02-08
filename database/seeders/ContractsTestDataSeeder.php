<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\Package;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Profession;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\Rental\RentalContract;
use App\Models\Sales\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Contracts Test Data Seeder
 *
 * Creates test data for:
 * - Recruitment Contracts (عقود الاستقدام)
 * - Rental Contracts (عقود الإيجار)
 *
 * Run: php artisan db:seed --class=ContractsTestDataSeeder
 */
class ContractsTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating Test Data for Contracts...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        // Get required data
        $branch = Branch::where('status', 'active')->first();
        $country = Country::where('is_active', true)->first();
        $profession = Profession::where('is_active', true)->first();
        $user = User::first();
        $client = Client::first();
        $customer = Customer::first();
        $worker = Laborer::first();
        $package = Package::where('type', 'rental')->where('status', 'active')->first();

        if (!$branch || !$country || !$user) {
            $this->command->error('Missing required data! Please seed branches, countries, and users first.');
            return;
        }

        if (!$client) {
            $this->command->error('No clients found. Please seed clients first.');
            return;
        }

        if (!$customer) {
            $this->command->warn('No customers found. Rental contracts will be created without customer.');
        }

        if (!$package) {
            $this->command->error('No rental packages found. Please seed packages first.');
            return;
        }

        if (!$worker) {
            $this->command->warn('No workers found. Contracts will be created without worker.');
        }

        // ============================================
        // Recruitment Contracts
        // ============================================
        $this->command->info('Step 1: Creating Recruitment Contracts...');

        $recruitmentStatuses = ['new', 'processing', 'contract_signed', 'ticket_booked', 'worker_received', 'closed'];
        $paymentStatuses = ['unpaid', 'partial', 'paid'];

        $recruitmentContracts = [];

        for ($i = 1; $i <= 15; $i++) {
            $status = $recruitmentStatuses[array_rand($recruitmentStatuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $directCost = rand(5000, 15000);
            $internalTicketCost = rand(2000, 5000);
            $externalCost = rand(1000, 3000);
            $vatCost = ($directCost + $internalTicketCost + $externalCost) * 0.15;
            $govCost = rand(500, 2000);
            $totalCost = $directCost + $internalTicketCost + $externalCost + $vatCost + $govCost;

            $paidTotal = 0;
            if ($paymentStatus === 'paid') {
                $paidTotal = $totalCost;
            } elseif ($paymentStatus === 'partial') {
                $paidTotal = $totalCost * 0.5;
            }

            $remainingTotal = $totalCost - $paidTotal;

            $contract = RecruitmentContract::create([
                'client_id' => $client?->id,
                'branch_id' => $branch->id,
                'gregorian_request_date' => Carbon::now()->subDays(rand(1, 90)),
                'hijri_request_date' => null,
                'visa_type' => ['paid', 'qualification', 'other'][array_rand(['paid', 'qualification', 'other'])],
                'visa_no' => 'VISA-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'visa_date' => Carbon::now()->subDays(rand(1, 60)),
                'arrival_country_id' => $country->id,
                'departure_country_id' => $country->id,
                'profession_id' => $profession?->id,
                'gender' => ['male', 'female'][array_rand(['male', 'female'])],
                'experience' => rand(1, 10) . ' years',
                'religion' => ['Muslim', 'Christian'][array_rand(['Muslim', 'Christian'])],
                'workplace_ar' => 'مكان العمل ' . $i,
                'workplace_en' => 'Workplace ' . $i,
                'monthly_salary' => rand(2000, 5000),
                'musaned_contract_no' => 'MUS-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'musaned_auth_no' => 'AUTH-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'musaned_contract_date' => Carbon::now()->subDays(rand(1, 30)),
                'direct_cost' => $directCost,
                'internal_ticket_cost' => $internalTicketCost,
                'external_cost' => $externalCost,
                'vat_cost' => $vatCost,
                'gov_cost' => $govCost,
                'total_cost' => $totalCost,
                'paid_total' => $paidTotal,
                'remaining_total' => $remainingTotal,
                'payment_status' => $paymentStatus,
                'status' => $status,
                'notes' => 'Test contract ' . $i . ' - ' . ($status === 'worker_received' ? 'This contract has been received.' : 'Test notes.'),
                'worker_id' => $worker?->id,
                'created_by' => $user->id,
               ]);

            $recruitmentContracts[] = $contract;
        }

        $this->command->info("✓ Created " . count($recruitmentContracts) . " Recruitment Contracts");
        $this->command->newLine();

        // ============================================
        // Rental Contracts
        // ============================================
        $this->command->info('Step 2: Creating Rental Contracts...');

        $rentalStatuses = ['active', 'suspended', 'completed', 'cancelled', 'returned', 'archived'];
        $rentalPaymentStatuses = ['paid', 'unpaid', 'partial', 'refunded'];

        $rentalContracts = [];

        for ($i = 1; $i <= 12; $i++) {
            $status = $rentalStatuses[array_rand($rentalStatuses)];
            $paymentStatus = $rentalPaymentStatuses[array_rand($rentalPaymentStatuses)];

            $durationType = ['day', 'month', 'year'][array_rand(['day', 'month', 'year'])];
            $duration = $durationType === 'day' ? rand(7, 30) : ($durationType === 'month' ? rand(1, 12) : rand(1, 2));

            $startDate = Carbon::now()->subDays(rand(1, 180));
            $endDate = clone $startDate;

            if ($durationType === 'day') {
                $endDate->addDays($duration);
            } elseif ($durationType === 'month') {
                $endDate->addMonths($duration);
            } else {
                $endDate->addYears($duration);
            }

            $subtotal = rand(3000, 10000);
            $taxPercent = 15;
            $taxValue = $subtotal * ($taxPercent / 100);
            $total = $subtotal + $taxValue;

            $paidTotal = 0;
            if ($paymentStatus === 'paid') {
                $paidTotal = $total;
            } elseif ($paymentStatus === 'partial') {
                $paidTotal = $total * 0.5;
            } elseif ($paymentStatus === 'refunded') {
                $paidTotal = 0;
            }

            $remainingTotal = $total - $paidTotal;

            $contract = RentalContract::create([
                'request_no' => 'REQ-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'branch_id' => $branch->id,
                'customer_id' => $customer?->id,
                'worker_id' => $worker?->id,
                'country_id' => $country->id,
                'profession_id' => $profession?->id,
                'package_id' => $package->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_type' => $durationType,
                'duration' => $duration,
                'tax_percent' => $taxPercent,
                'discount_type' => 'none',
                'discount_value' => 0,
                'subtotal' => $subtotal,
                'tax_value' => $taxValue,
                'total' => $total,
                'paid_total' => $paidTotal,
                'remaining_total' => $remainingTotal,
                'notes' => 'Test rental contract ' . $i . ' - Status: ' . $status,
                'created_by' => $user->id,
            ]);

            $rentalContracts[] = $contract;
        }

        $this->command->info("✓ Created " . count($rentalContracts) . " Rental Contracts");
        $this->command->newLine();

        // ============================================
        // Summary
        // ============================================
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  TEST DATA SUMMARY');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("  Recruitment Contracts: " . count($recruitmentContracts));
        $this->command->info("  Rental Contracts: " . count($rentalContracts));
        $this->command->info("  Total Contracts: " . (count($recruitmentContracts) + count($rentalContracts)));
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        // Status breakdown
        $recruitmentStatusCount = [];
        foreach ($recruitmentContracts as $contract) {
            $recruitmentStatusCount[$contract->status] = ($recruitmentStatusCount[$contract->status] ?? 0) + 1;
        }

        $rentalStatusCount = [];
        foreach ($rentalContracts as $contract) {
            $rentalStatusCount[$contract->status] = ($rentalStatusCount[$contract->status] ?? 0) + 1;
        }

        $this->command->info('Recruitment Contracts by Status:');
        foreach ($recruitmentStatusCount as $status => $count) {
            $this->command->info("  - {$status}: {$count}");
        }

        $this->command->newLine();
        $this->command->info('Rental Contracts by Status:');
        foreach ($rentalStatusCount as $status => $count) {
            $this->command->info("  - {$status}: {$count}");
        }

        $this->command->newLine();
        $this->command->info('✓ Test data created successfully!');
    }
}
