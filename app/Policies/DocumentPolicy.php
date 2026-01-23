<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;

class DocumentPolicy
{
    /**
     * View document list
     * Admin: can see all
     * Office: handled in controller (own docs only)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('office');
    }

    /**
     * View a single document
     * Admin: any document
     * Office: only own documents
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $document->created_by === $user->id;
    }

    /**
     * Create / submit documents
     * Office users only
     */
    public function create(User $user): bool
    {
        return $user->hasRole('office');
    }

    /**
     * Acknowledge memo
     * Admin only
     * Must be a memo
     */
    public function acknowledge(User $user, Document $document): bool
    {
        return $user->hasRole('admin') && $document->is_memo;
    }

    /**
     * Reject document
     * Admin only
     */
    public function reject(User $user, Document $document): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Delete document
     * Admin only
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->hasRole('admin');
    }
}
