<?php

namespace App\Policies\Accounting;

use App\Models\Accounting\JournalEntry;
use App\Models\User;
use App\Enums\Accounting\JournalEntryStatus;

class JournalEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('journal_entries.view_any');
    }

    public function view(User $user, JournalEntry $journalEntry): bool
    {
        return $user->can('journal_entries.view');
    }

    public function create(User $user): bool
    {
        return $user->can('journal_entries.create');
    }

    public function update(User $user, JournalEntry $journalEntry): bool
    {
        if ($journalEntry->is_posted) {
            return false;
        }
        
        $status = JournalEntryStatus::from($journalEntry->status ?? JournalEntryStatus::DRAFT->value);
        if (!$status->canBeEdited()) {
            return false;
        }
        
        return $user->can('journal_entries.update');
    }

    public function delete(User $user, JournalEntry $journalEntry): bool
    {
        if ($journalEntry->is_posted) {
            return false;
        }
        
        $status = JournalEntryStatus::from($journalEntry->status ?? JournalEntryStatus::DRAFT->value);
        if (!$status->canBeDeleted()) {
            return false;
        }
        
        return $user->can('journal_entries.delete');
    }

    public function submit(User $user, JournalEntry $journalEntry): bool
    {
        $status = JournalEntryStatus::from($journalEntry->status ?? JournalEntryStatus::DRAFT->value);
        return $status->canBeSubmitted() && $user->can('journal_entries.submit');
    }

    public function approve(User $user, JournalEntry $journalEntry): bool
    {
        $status = JournalEntryStatus::from($journalEntry->status ?? JournalEntryStatus::DRAFT->value);
        return $status->canBeApproved() && $user->can('journal_entries.approve');
    }

    public function reject(User $user, JournalEntry $journalEntry): bool
    {
        $status = JournalEntryStatus::from($journalEntry->status ?? JournalEntryStatus::DRAFT->value);
        return $status->canBeApproved() && $user->can('journal_entries.reject');
    }

    public function post(User $user, JournalEntry $journalEntry): bool
    {
        $status = JournalEntryStatus::from($journalEntry->status ?? JournalEntryStatus::DRAFT->value);
        return !$journalEntry->is_posted && $status->canBePosted() && $user->can('journal_entries.post');
    }
}
