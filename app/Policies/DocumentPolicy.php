<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any documents.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(['admin', 'approver', 'creator', 'viewer']);
    }

    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document)
    {
        // Admins see all
        if ($user->hasRole('admin')) return true;

        // Creator can see own
        if ($user->hasRole('creator') && $document->created_by == $user->id) return true;

        // Approver can see documents pending approval
        if ($user->hasRole('approver') && $document->status == 'pending') return true;

        // Viewers can see all approved documents
        if ($user->hasRole('viewer') && $document->status == 'approved') return true;

        return false;
    }

    /**
     * Determine whether the user can create documents.
     */
    public function create(User $user)
    {
        return $user->hasRole(['admin', 'creator']);
    }

    /**
     * Determine whether the user can update the document.
     */
    public function update(User $user, Document $document)
    {
        // Admins can update all
        if ($user->hasRole('admin')) return true;

        // Creator can update own if not approved
        return $user->hasRole('creator') && $document->created_by == $user->id && $document->status == 'pending';
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document)
    {
        // Admins can delete all
        if ($user->hasRole('admin')) return true;

        // Creator can delete own if not approved
        return $user->hasRole('creator') && $document->created_by == $user->id && $document->status == 'pending';
    }

    /**
     * Determine whether the user can approve the document.
     */
    public function approve(User $user, Document $document)
    {
        return $user->hasRole('approver') && $document->status == 'pending';
    }

    /**
     * Determine whether the user can reject the document.
     */
    public function reject(User $user, Document $document)
    {
        return $user->hasRole('approver') && $document->status == 'pending';
    }
}
