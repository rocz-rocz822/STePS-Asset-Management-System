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
        return true; // Admin, Technician, and Staff can all add assets
    }

    public function update(User $user, Asset $asset): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Technician and Staff: only assets assigned to them
        return $asset->assigned_to === $user->id;
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