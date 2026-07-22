<?php

namespace App\Policies;

use App\Models\AssetHistory;
use App\Models\User;

class AssetHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // page is accessible to everyone; query results are scoped per role
    }

    public function view(User $user, AssetHistory $history): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $history->asset->created_by === $user->id;
    }
}