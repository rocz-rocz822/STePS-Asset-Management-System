<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Anyone logged in can view the asset list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Anyone logged in can view asset details.
     */
    public function view(User $user, Asset $asset): bool
    {
        return true;
    }

    /**
     * Admins and technicians can create assets.
     */
    public function create(User $user): bool
    {
        return $user->canManageAssets();
    }

    /**
     * Admins can edit any asset.
     * Technicians can edit only assets they created.
     */
    public function update(User $user, Asset $asset): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can_manage_assets
            && $asset->created_by === $user->id;
    }

    /**
     * Only administrators can soft delete assets.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only administrators can restore deleted assets.
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only administrators can access the Trash page.
     */
    public function viewTrashed(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Attachment permissions follow update permissions.
     */
    public function manageAttachments(User $user, Asset $asset): bool
    {
        return $this->update($user, $asset);
    }
}