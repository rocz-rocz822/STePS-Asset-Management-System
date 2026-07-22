<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only administrators can view the user list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only administrators can view user details.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only administrators can create users.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only administrators can update users.
     */
    public function update(User $user, User $model): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        // Protected accounts can only be edited by themselves.
        if ($model->is_protected && $user->id !== $model->id) {
            return false;
        }

        return true;
    }
    /**
     * Only administrators can delete users.
     */
    public function delete(User $user, User $model): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        // Protected accounts can never be deleted.
        if ($model->is_protected) {
            return false;
        }

        // Cannot delete yourself.
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot delete the last remaining administrator.
        if ($model->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return false;
        }

        return true;
    }

    /**
     * Only administrators can activate/deactivate users.
     */
    public function toggleStatus(User $user, User $model): bool
    {
        // Protected accounts can never be deactivated.
        if ($model->is_protected) {
            return false;
        }

        return $user->isAdmin() && $user->id !== $model->id;
    }
}