<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Anyone logged in can create documents
     */
    public function create(User $user): bool
    {
        return true; // admin + regular users
    }

    /**
     * View document
     */
    public function view(User $user, Document $document): bool
    {
        // Admin sees all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Creator always sees
        if ($document->created_by === $user->id) {
            return true;
        }

        // Memo visibility
        if ($document->is_memo) {
            if ($document->audience_type === 'all') return true;
            if ($document->audience_type === 'users') {
                return is_array($document->audience_users)
                    && in_array($user->id, $document->audience_users);
            }
        }

        // Approval visibility
        if ($document->is_for_approval) {
            if ($document->current_approver_id === $user->id) return true;
            if (
                is_array($document->next_approver_ids)
                && in_array($user->id, $document->next_approver_ids)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update
     */
    public function update(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) return true;

        // creator can edit only if not completed
        return $document->created_by === $user->id
            && !in_array($document->status, ['completed', 'rejected']);
    }

    /**
     * Delete
     */
    public function delete(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) return true;

        return $document->created_by === $user->id
            && !in_array($document->status, ['completed']);
    }

    /**
     * Approve
     */
    public function approve(User $user, Document $document)
    {
        return
            $user->hasRole('admin') ||
            $document->current_approver_id == $user->id;
    }


    /**
     * Reject
     */
    public function reject(User $user, Document $document): bool
    {
        return $this->approve($user, $document);
    }
}
