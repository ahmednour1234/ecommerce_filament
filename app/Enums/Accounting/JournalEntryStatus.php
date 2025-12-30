<?php

namespace App\Enums\Accounting;

enum JournalEntryStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case POSTED = 'posted';

    /**
     * Get label for display
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => trans_dash('accounting.status.draft', 'Draft'),
            self::PENDING_APPROVAL => trans_dash('accounting.status.pending_approval', 'Pending Approval'),
            self::APPROVED => trans_dash('accounting.status.approved', 'Approved'),
            self::REJECTED => trans_dash('accounting.status.rejected', 'Rejected'),
            self::POSTED => trans_dash('accounting.status.posted', 'Posted'),
        };
    }

    /**
     * Get color for badge
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PENDING_APPROVAL => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::POSTED => 'info',
        };
    }

    /**
     * Check if entry can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED]);
    }

    /**
     * Check if entry can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this === self::DRAFT;
    }

    /**
     * Check if entry can be submitted for approval
     */
    public function canBeSubmitted(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED]);
    }

    /**
     * Check if entry can be approved
     */
    public function canBeApproved(): bool
    {
        return $this === self::PENDING_APPROVAL;
    }

    /**
     * Check if entry can be posted
     */
    public function canBePosted(): bool
    {
        return $this === self::APPROVED;
    }
}

