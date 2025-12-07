<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\Journal;
use App\Models\Accounting\JournalEntry;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\User;
use Illuminate\Database\Seeder;

class JournalEntrySeeder extends Seeder
{
    public function run(): void
    {
        $journal = Journal::where('code', 'GEN')->first();
        $branch = Branch::first();
        $costCenter = \App\Models\MainCore\CostCenter::first();
        $user = User::first();

        if (!$journal || !$user) {
            return; // Skip if required dependencies don't exist
        }

        $entries = [
            [
                'journal_id' => $journal->id,
                'entry_number' => 'JE-001',
                'entry_date' => now()->subDays(5),
                'reference' => 'REF-001',
                'description' => 'Initial capital entry',
                'branch_id' => $branch?->id,
                'cost_center_id' => $costCenter?->id,
                'user_id' => $user->id,
                'is_posted' => true,
                'posted_at' => now()->subDays(5),
            ],
        ];

        foreach ($entries as $entry) {
            JournalEntry::updateOrCreate(
                ['entry_number' => $entry['entry_number']],
                $entry
            );
        }
    }
}

