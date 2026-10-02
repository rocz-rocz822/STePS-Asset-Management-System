<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // everyone can open the Assets page — the list itself is scoped in the controller
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->isAdmin() || $asset->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        if ($user->isStaff()) {
            return true; // Staff can add assets, always assigned to themselves
        }

        return $user->isAdmin() || $user->can_manage_assets;
    }

    public function update(User $user, Asset $asset): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStaff()) {
            return $asset->assigned_to === $user->id;
        }

        return $user->can_manage_assets && $asset->assigned_to === $user->id;
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    public function viewTrashed(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageAttachments(User $user, Asset $asset): bool
    {
        return $this->update($user, $asset);
    }

    public function forceStatus(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }
}