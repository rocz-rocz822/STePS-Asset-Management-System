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
        if ($user->isAdmin() || $user->isStaff()) {
            return true;
        }

        return $user->can_manage_assets;
    }

    public function update(User $user, MaintenanceRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $record->created_by === $user->id && ($user->isStaff() || $user->can_manage_assets);
    }
}