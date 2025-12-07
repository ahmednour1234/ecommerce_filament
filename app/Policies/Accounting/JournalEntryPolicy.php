<?php

namespace App\Policies\Accounting;

use App\Models\Accounting\JournalEntry;
use App\Models\User;

class JournalEntryPolicy
{
    /**
     * Determine if the user can view any journal entries.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('journal_entries.view_any');
    }

    /**
     * Determine if the user can view the journal entry.
     */
    public function view(User $user, JournalEntry $journalEntry): bool
    {
        if (!$user->can('journal_entries.view')) {
            return false;
        }

        // Branch-aware: If user has specific branches, only show entries from those branches
        if (!$user->hasRole('super_admin')) {
            $userBranchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($userBranchIds) && !in_array($journalEntry->branch_id, $userBranchIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the user can create journal entries.
     */
    public function create(User $user): bool
    {
        return $user->can('journal_entries.create');
    }

    /**
     * Determine if the user can update the journal entry.
     */
    public function update(User $user, JournalEntry $journalEntry): bool
    {
        if (!$user->can('journal_entries.update')) {
            return false;
        }

        // Cannot edit posted entries
        if ($journalEntry->is_posted) {
            return false;
        }

        // Branch-aware filtering
        if (!$user->hasRole('super_admin')) {
            $userBranchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($userBranchIds) && !in_array($journalEntry->branch_id, $userBranchIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the user can delete the journal entry.
     */
    public function delete(User $user, JournalEntry $journalEntry): bool
    {
        if (!$user->can('journal_entries.delete')) {
            return false;
        }

        // Cannot delete posted entries
        if ($journalEntry->is_posted) {
            return false;
        }

        // Branch-aware filtering
        if (!$user->hasRole('super_admin')) {
            $userBranchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($userBranchIds) && !in_array($journalEntry->branch_id, $userBranchIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the user can post the journal entry.
     */
    public function post(User $user, JournalEntry $journalEntry): bool
    {
        return $user->can('journal_entries.post');
    }
}

