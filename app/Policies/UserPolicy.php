<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * View users list
     */
    public function viewAny(User $user): bool
    {
        
        return $user->hasRole('admin');
    }

    /**
     * View a specific user
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Create users
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Update users
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Delete users
     * (Prevent deleting yourself)
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $user->id !== $model->id;
    }
}
