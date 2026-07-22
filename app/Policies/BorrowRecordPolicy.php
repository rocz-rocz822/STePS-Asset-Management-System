<?php

namespace App\Policies;

use App\Models\BorrowRecord;
use App\Models\User;

class BorrowRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BorrowRecord $record): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageAssets();
    }

    public function update(User $user, BorrowRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can_manage_assets && $record->created_by === $user->id;
    }
}