<?php

namespace Tests\Unit\Accounting;

use Tests\TestCase;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Accounting\Account;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Services\Accounting\PostingService;
use App\Enums\Accounting\JournalEntryStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PostingService $postingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postingService = app(PostingService::class);
    }

    /** @test */
    public function it_can_post_a_balanced_journal_entry()
    {
        // Create accounts
        $cashAccount = Account::create([
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
            'allow_manual_entry' => true,
        ]);
        
        $revenueAccount = Account::create([
            'code' => '4000',
            'name' => 'Revenue',
            'type' => 'revenue',
            'is_active' => true,
            'allow_manual_entry' => true,
        ]);

        // Create a balanced journal entry
        $entry = JournalEntry::create([
            'journal_id' => 1, // Assuming journal exists
            'entry_number' => 'JE-TEST-001',
            'entry_date' => now(),
            'status' => JournalEntryStatus::APPROVED,
            'user_id' => 1,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $cashAccount->id,
            'debit' => 1000.00,
            'credit' => 0.00,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $revenueAccount->id,
            'debit' => 0.00,
            'credit' => 1000.00,
        ]);

        // Post the entry
        $result = $this->postingService->postEntry($entry->id);

        $this->assertTrue($result);
        $this->assertEquals(JournalEntryStatus::POSTED, $entry->fresh()->status);
        $this->assertTrue($entry->fresh()->is_posted);

        // Verify GL entries were created
        $this->assertDatabaseHas('general_ledger_entries', [
            'journal_entry_id' => $entry->id,
            'account_id' => $cashAccount->id,
            'debit' => 1000.00,
        ]);

        $this->assertDatabaseHas('general_ledger_entries', [
            'journal_entry_id' => $entry->id,
            'account_id' => $revenueAccount->id,
            'credit' => 1000.00,
        ]);
    }

    /** @test */
    public function it_cannot_post_an_unbalanced_journal_entry()
    {
        $cashAccount = Account::create([
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);
        
        $revenueAccount = Account::create([
            'code' => '4000',
            'name' => 'Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);

        $entry = JournalEntry::create([
            'journal_id' => 1,
            'entry_number' => 'JE-TEST-002',
            'entry_date' => now(),
            'status' => JournalEntryStatus::APPROVED,
            'user_id' => 1,
        ]);

        // Create unbalanced entry
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $cashAccount->id,
            'debit' => 1000.00,
            'credit' => 0.00,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $revenueAccount->id,
            'debit' => 0.00,
            'credit' => 500.00, // Not balanced!
        ]);

        $this->expectException(\Exception::class);
        $this->postingService->postEntry($entry->id);

        $this->assertEquals(JournalEntryStatus::APPROVED, $entry->fresh()->status);
        $this->assertFalse($entry->fresh()->is_posted);
    }

    /** @test */
    public function it_cannot_post_an_unapproved_entry()
    {
        $entry = JournalEntry::create([
            'journal_id' => 1,
            'entry_number' => 'JE-TEST-003',
            'entry_date' => now(),
            'status' => JournalEntryStatus::DRAFT,
            'user_id' => 1,
        ]);

        $this->expectException(\Exception::class);
        $this->postingService->postEntry($entry->id);
    }

    /** @test */
    public function it_cannot_post_an_already_posted_entry()
    {
        $entry = JournalEntry::create([
            'journal_id' => 1,
            'entry_number' => 'JE-TEST-004',
            'entry_date' => now(),
            'status' => JournalEntryStatus::POSTED,
            'is_posted' => true,
            'user_id' => 1,
        ]);

        $this->expectException(\Exception::class);
        $this->postingService->postEntry($entry->id);
    }
}

