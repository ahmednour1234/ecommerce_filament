<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use Illuminate\Database\Seeder;

class JournalEntryLineSeeder extends Seeder
{
    public function run(): void
    {
        $journalEntry = JournalEntry::where('entry_number', 'JE-001')->first();
        $cashAccount = Account::where('code', '1.1.1')->first();
        $equityAccount = Account::where('code', '3')->first() ?? Account::where('type', 'equity')->first();
        $branch = Branch::first();
        $costCenter = \App\Models\MainCore\CostCenter::first();

        if (!$journalEntry || !$cashAccount) {
            return; // Skip if required dependencies don't exist
        }

        // If equity account doesn't exist, create it
        if (!$equityAccount) {
            $equityAccount = Account::updateOrCreate(
                ['code' => '3'],
                [
                    'name' => 'Equity',
                    'type' => 'equity',
                    'level' => 1,
                    'is_active' => true,
                    'allow_manual_entry' => false,
                ]
            );
        }

        $lines = [
            [
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $cashAccount->id,
                'debit' => 10000.00,
                'credit' => 0.00,
                'description' => 'Initial capital contribution',
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
            ],
            [
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $equityAccount->id,
                'debit' => 0.00,
                'credit' => 10000.00,
                'description' => 'Initial capital contribution',
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
            ],
        ];

        foreach ($lines as $line) {
            JournalEntryLine::updateOrCreate(
                [
                    'journal_entry_id' => $line['journal_entry_id'],
                    'account_id' => $line['account_id'],
                ],
                $line
            );
        }
    }
}

