<?php

namespace App\Policies;

use App\Models\MaintenanceRecord;
use App\Models\User;

class MaintenanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaintenanceRecord $record): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageAssets();
    }

    public function update(User $user, MaintenanceRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can_manage_assets && $record->created_by === $user->id;
    }
}