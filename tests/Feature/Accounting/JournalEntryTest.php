<?php

namespace Tests\Feature\Accounting;

use Tests\TestCase;
use App\Models\User;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\Account;
use App\Models\Accounting\Journal;
use App\Enums\Accounting\JournalEntryStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_create_a_balanced_journal_entry()
    {
        $journal = Journal::factory()->create();
        $cashAccount = Account::factory()->create(['type' => 'asset', 'code' => '1000']);
        $revenueAccount = Account::factory()->create(['type' => 'revenue', 'code' => '4000']);

        $response = $this->post(route('filament.admin.resources.accounting.journal-entries.store'), [
            'journal_id' => $journal->id,
            'entry_number' => 'JE-000001',
            'entry_date' => now()->format('Y-m-d'),
            'description' => 'Test Entry',
            'lines' => [
                [
                    'account_id' => $cashAccount->id,
                    'debit' => 1000.00,
                    'credit' => 0.00,
                    'description' => 'Cash received',
                ],
                [
                    'account_id' => $revenueAccount->id,
                    'debit' => 0.00,
                    'credit' => 1000.00,
                    'description' => 'Revenue',
                ],
            ],
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'entry_number' => 'JE-000001',
            'status' => JournalEntryStatus::DRAFT->value,
        ]);

        $entry = JournalEntry::where('entry_number', 'JE-000001')->first();
        $this->assertCount(2, $entry->lines);
    }

    /** @test */
    public function it_validates_balance_on_create()
    {
        $journal = Journal::factory()->create();
        $cashAccount = Account::factory()->create(['type' => 'asset', 'code' => '1000']);
        $revenueAccount = Account::factory()->create(['type' => 'revenue', 'code' => '4000']);

        $response = $this->post(route('filament.admin.resources.accounting.journal-entries.store'), [
            'journal_id' => $journal->id,
            'entry_number' => 'JE-000002',
            'entry_date' => now()->format('Y-m-d'),
            'description' => 'Unbalanced Entry',
            'lines' => [
                [
                    'account_id' => $cashAccount->id,
                    'debit' => 1000.00,
                    'credit' => 0.00,
                ],
                [
                    'account_id' => $revenueAccount->id,
                    'debit' => 0.00,
                    'credit' => 500.00, // Not balanced!
                ],
            ],
        ]);

        $response->assertSessionHasErrors();
    }
}

