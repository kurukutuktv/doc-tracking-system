<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Helper: Check if user belongs to Administrative Office
     */
    protected function isAdminOffice(User $user): bool
    {
        return $user->department
            && $user->department->code === 'ADM';
    }

    /* =========================================================
       ADMINISTRATIVE OFFICE ACTIONS
       ========================================================= */

    /**
     * Receive / create incoming document
     */
    public function receive(User $user): bool
    {
        return $this->isAdminOffice($user);
    }

    /**
     * Log & stamp document (generate control number)
     */
    public function log(User $user, Document $document): bool
    {
        return $this->isAdminOffice($user)
            && $document->status->code === 'RECEIVED';
    }

    /**
     * Forward document to concerned office
     */
    public function forward(User $user, Document $document): bool
    {
        return $this->isAdminOffice($user)
            && $document->status->code === 'LOGGED';
    }

    /**
     * Transmit outgoing document
     */
    public function transmit(User $user, Document $document): bool
    {
        return $this->isAdminOffice($user)
            && $document->direction === 'OUTGOING'
            && $document->status->code === 'LOGGED';
    }

    /**
     * Archive document (FINAL STATE)
     */
    public function archive(User $user, Document $document): bool
    {
        return $this->isAdminOffice($user)
            && $document->status->code === 'TRANSMITTED';
    }

    /* =========================================================
       CONCERNED OFFICE ACTIONS
       ========================================================= */

    /**
     * View document forwarded to their office
     */
    public function view(User $user, Document $document): bool
    {
        return $user->department_id === $document->current_office_id;
    }

    /**
     * Acknowledge receipt
     */
    public function acknowledge(User $user, Document $document): bool
    {
        return $user->department_id === $document->current_office_id
            && $document->status->code === 'FORWARDED';
    }

    /**
     * Prepare reply (create outgoing document)
     */
    public function prepareReply(User $user, Document $document): bool
    {
        return $user->department_id === $document->current_office_id
            && in_array($document->status->code, ['ACKNOWLEDGED', 'FOR_REPLY']);
    }

    /* =========================================================
       COMMON RULES
       ========================================================= */

    /**
     * Prevent edits on archived documents
     */
    public function update(User $user, Document $document): bool
    {
        return $document->status->code !== 'ARCHIVED';
    }
}
