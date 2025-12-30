<?php

namespace Tests\Unit\Accounting;

use Tests\TestCase;
use App\Models\Accounting\Account;
use App\Models\Accounting\GeneralLedgerEntry;
use App\Models\Accounting\JournalEntry;
use App\Services\Accounting\GeneralLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneralLedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GeneralLedgerService $glService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->glService = app(GeneralLedgerService::class);
    }

    /** @test */
    public function it_calculates_account_balance_correctly()
    {
        $account = Account::create([
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $entry1 = JournalEntry::create([
            'journal_id' => 1,
            'entry_number' => 'JE-GL-001',
            'entry_date' => '2024-01-01',
            'is_posted' => true,
            'user_id' => 1,
        ]);
        
        GeneralLedgerEntry::create([
            'account_id' => $account->id,
            'journal_entry_id' => $entry1->id,
            'debit' => 1000.00,
            'credit' => 0.00,
            'balance' => 1000.00,
            'entry_date' => '2024-01-01',
        ]);

        $entry2 = JournalEntry::create([
            'journal_id' => 1,
            'entry_number' => 'JE-GL-002',
            'entry_date' => '2024-01-15',
            'is_posted' => true,
            'user_id' => 1,
        ]);
        
        GeneralLedgerEntry::create([
            'account_id' => $account->id,
            'journal_entry_id' => $entry2->id,
            'debit' => 0.00,
            'credit' => 300.00,
            'balance' => 700.00,
            'entry_date' => '2024-01-15',
        ]);

        $balance = $this->glService->getAccountBalance($account->id);

        $this->assertEquals(700.00, $balance);
    }

    /** @test */
    public function it_calculates_balance_up_to_specific_date()
    {
        $account = Account::create([
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);

        GeneralLedgerEntry::create([
            'account_id' => $account->id,
            'journal_entry_id' => 1,
            'debit' => 1000.00,
            'credit' => 0.00,
            'balance' => 1000.00,
            'entry_date' => '2024-01-01',
        ]);

        GeneralLedgerEntry::create([
            'account_id' => $account->id,
            'journal_entry_id' => 2,
            'debit' => 0.00,
            'credit' => 300.00,
            'balance' => 700.00,
            'entry_date' => '2024-01-15',
        ]);

        GeneralLedgerEntry::create([
            'account_id' => $account->id,
            'journal_entry_id' => 3,
            'debit' => 500.00,
            'credit' => 0.00,
            'balance' => 1200.00,
            'entry_date' => '2024-01-20',
        ]);

        $balance = $this->glService->getAccountBalance($account->id, new \DateTime('2024-01-10'));

        $this->assertEquals(1000.00, $balance);
    }

    /** @test */
    public function it_returns_zero_for_account_with_no_entries()
    {
        $account = Account::create([
            'code' => '2000',
            'name' => 'Test Account',
            'type' => 'liability',
            'is_active' => true,
        ]);

        $balance = $this->glService->getAccountBalance($account->id);

        $this->assertEquals(0.0, $balance);
    }
}

