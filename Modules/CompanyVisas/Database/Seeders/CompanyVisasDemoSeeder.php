<?php

namespace Modules\CompanyVisas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CompanyVisas\Entities\CompanyVisaRequest;
use Modules\CompanyVisas\Entities\CompanyVisaContract;
use Modules\CompanyVisas\Entities\CompanyVisaContractExpense;
use Modules\CompanyVisas\Entities\CompanyVisaContractCost;
use Modules\CompanyVisas\Entities\CompanyVisaContractDocument;
use App\Models\Recruitment\Profession;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Laborer;
use App\Models\MainCore\Country;
use App\Models\Accounting\Account;
use App\Models\MainCore\PaymentMethod;
use App\Models\User;

class CompanyVisasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Company Visas demo data...');

        $profession = Profession::where('is_active', true)->first();
        $nationality = \App\Models\Recruitment\Nationality::where('is_active', true)->first();
        $agent = Agent::first();
        $country = Country::first();
        $user = User::first();

        if (!$profession || !$nationality || !$agent || !$country || !$user) {
            $this->command->warn('Required data not found. Please seed professions, nationalities, agents, countries, and users first.');
            return;
        }

        $expenseAccount = Account::active()->first();
        $paymentMethod = PaymentMethod::where('is_active', true)->first();

        $requests = [];
        for ($i = 1; $i <= 5; $i++) {
            $request = CompanyVisaRequest::create([
                'code' => \Modules\CompanyVisas\Services\CompanyVisaRequestService::generateCode(),
                'request_date' => now()->subDays(rand(1, 30)),
                'profession_id' => $profession->id,
                'nationality_id' => $nationality->id,
                'gender' => rand(0, 1) ? 'male' : 'female',
                'workers_count' => rand(10, 50),
                'visa_number' => 'VISA-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'status' => ['draft', 'paid', 'completed', 'rejected'][rand(0, 3)],
                'notes' => "Demo visa request #{$i}",
                'created_by' => $user->id,
            ]);
            $request->remaining_count = $request->workers_count - $request->used_count;
            $request->save();
            $requests[] = $request;
        }

        $this->command->info('✓ Created 5 visa requests');

        $contracts = [];
        foreach (array_slice($requests, 0, 2) as $request) {
            $contract = CompanyVisaContract::create([
                'contract_no' => \Modules\CompanyVisas\Services\CompanyVisaContractService::generateContractNo(),
                'contract_date' => now()->subDays(rand(1, 15)),
                'visa_request_id' => $request->id,
                'agent_id' => $agent->id,
                'profession_id' => $profession->id,
                'country_id' => $country->id,
                'workers_required' => rand(5, min(20, $request->remaining_count)),
                'status' => ['draft', 'active', 'completed'][rand(0, 2)],
                'notes' => 'Demo contract',
                'created_by' => $user->id,
            ]);
            $contracts[] = $contract;
        }

        $this->command->info('✓ Created 2 contracts');

        if (!empty($contracts) && $expenseAccount && $paymentMethod) {
            foreach ($contracts as $contract) {
                CompanyVisaContractExpense::create([
                    'contract_id' => $contract->id,
                    'expense_account_id' => $expenseAccount->id,
                    'amount' => rand(1000, 5000),
                    'includes_vat' => rand(0, 1),
                    'expense_date' => now()->subDays(rand(1, 10)),
                    'payment_method_id' => $paymentMethod->id,
                    'invoice_no' => 'INV-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
                    'description' => 'Demo expense',
                    'created_by' => $user->id,
                ]);
            }
            $this->command->info('✓ Created sample expenses');
        }

        foreach ($contracts as $contract) {
            if ($contract->linked_workers_count > 0) {
                CompanyVisaContractCost::create([
                    'contract_id' => $contract->id,
                    'cost_per_worker' => rand(5000, 15000),
                    'total_cost' => $contract->linked_workers_count * rand(5000, 15000),
                    'due_date' => now()->addDays(rand(30, 90)),
                    'description' => 'Demo contract cost',
                    'created_by' => $user->id,
                ]);
            }
        }
        $this->command->info('✓ Created sample costs');

        $this->command->info('✓ Demo data created successfully');
    }
}
