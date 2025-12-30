<?php

namespace App\Services\Accounting;

use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\ApprovalLog;
use App\Models\User;
use App\Enums\Accounting\JournalEntryStatus;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Submit journal entry for approval
     */
    public function submitForApproval(JournalEntry $entry, ?User $user = null): void
    {
        $user = $user ?? auth()->user();
        $status = JournalEntryStatus::from($entry->status ?? JournalEntryStatus::DRAFT->value);
        
        if (!$status->canBeSubmitted()) {
            throw new \Exception(trans_dash('accounting.cannot_submit', 'Entry cannot be submitted in current status.'));
        }
        
        if (!$entry->isBalanced()) {
            throw new \Exception(trans_dash('accounting.entries_not_balanced', 'Entry must be balanced before submission.'));
        }
        
        DB::transaction(function () use ($entry, $user) {
            $entry->update([
                'status' => JournalEntryStatus::PENDING_APPROVAL->value,
            ]);
            
            $this->logApprovalAction($entry, 'submitted', $user);
        });
    }

    /**
     * Approve journal entry
     */
    public function approve(JournalEntry $entry, ?User $user = null, ?string $notes = null): void
    {
        $user = $user ?? auth()->user();
        $status = JournalEntryStatus::from($entry->status ?? JournalEntryStatus::DRAFT->value);
        
        if (!$status->canBeApproved()) {
            throw new \Exception(trans_dash('accounting.cannot_approve', 'Entry cannot be approved in current status.'));
        }
        
        DB::transaction(function () use ($entry, $user, $notes) {
            $entry->update([
                'status' => JournalEntryStatus::APPROVED->value,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            $this->logApprovalAction($entry, 'approved', $user, $notes);
        });
    }

    /**
     * Reject journal entry
     */
    public function reject(JournalEntry $entry, string $reason, ?User $user = null): void
    {
        $user = $user ?? auth()->user();
        $status = JournalEntryStatus::from($entry->status ?? JournalEntryStatus::DRAFT->value);
        
        if (!$status->canBeApproved()) {
            throw new \Exception(trans_dash('accounting.cannot_reject', 'Entry cannot be rejected in current status.'));
        }
        
        DB::transaction(function () use ($entry, $user, $reason) {
            $entry->update([
                'status' => JournalEntryStatus::REJECTED->value,
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);
            
            $this->logApprovalAction($entry, 'rejected', $user, $reason);
        });
    }

    /**
     * Log approval action
     */
    protected function logApprovalAction(JournalEntry $entry, string $action, User $user, ?string $notes = null): void
    {
        ApprovalLog::create([
            'approvable_type' => JournalEntry::class,
            'approvable_id' => $entry->id,
            'action' => $action,
            'user_id' => $user->id,
            'notes' => $notes,
            'metadata' => [
                'entry_number' => $entry->entry_number,
                'entry_date' => $entry->entry_date?->toDateString(),
            ],
        ]);
    }
}

