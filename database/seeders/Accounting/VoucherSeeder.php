<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Voucher;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\User;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $cashAccount = Account::where('code', '1.1.1')->first();
        $branch = Branch::first();
        $costCenter = \App\Models\MainCore\CostCenter::first();
        $user = User::first();

        if (!$cashAccount || !$user) {
            return; // Skip if required dependencies don't exist
        }

        $vouchers = [
            [
                'voucher_number' => 'VCH-001',
                'type' => 'receipt',
                'voucher_date' => now()->subDays(3),
                'amount' => 5000.00,
                'account_id' => $cashAccount->id,
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
                'description' => 'Customer payment received',
                'reference' => 'INV-001',
                'created_by' => $user->id,
            ],
            [
                'voucher_number' => 'VCH-002',
                'type' => 'payment',
                'voucher_date' => now()->subDays(2),
                'amount' => 2000.00,
                'account_id' => $cashAccount->id,
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
                'description' => 'Supplier payment',
                'reference' => 'PO-001',
                'created_by' => $user->id,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::updateOrCreate(
                ['voucher_number' => $voucher['voucher_number']],
                $voucher
            );
        }
    }
}

