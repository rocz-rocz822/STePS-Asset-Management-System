<?php

namespace App\Policies;

use App\Models\BorrowRecord;
use App\Models\User;

class BorrowRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return ! $user->isStaff();
    }

    public function view(User $user, BorrowRecord $record): bool
    {
        return ! $user->isStaff();
    }

    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStaff()) {
            return false;
        }

        return $user->can_manage_assets;
    }

    public function update(User $user, BorrowRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $record->created_by === $user->id && $user->can_manage_assets;
    }
}