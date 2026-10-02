<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case Borrowed = 'borrowed';
    case UnderMaintenance = 'under_maintenance';
    case UnderRepair = 'under_repair';
    case Lost = 'lost';
    case Disposed = 'disposed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Assigned => 'Assigned',
            self::Borrowed => 'Borrowed',
            self::UnderMaintenance => 'Under Maintenance',
            self::UnderRepair => 'Under Repair',
            self::Lost => 'Lost',
            self::Disposed => 'Disposed',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Assigned => 'blue',
            self::Borrowed => 'yellow',
            self::UnderMaintenance, self::UnderRepair => 'yellow',
            self::Lost, self::Disposed => 'red',
            self::Archived => 'gray',
        };
    }

    /**
     * Statuses a user can set directly on the Asset form.
     * Borrowed, UnderMaintenance, and UnderRepair are excluded —
     * those are controlled automatically by the Borrowing and
     * Maintenance modules. Admins can still set them via the
     * separate "Force Status" action when genuinely needed.
     */
    public static function manuallySettable(): array
    {
        return [
            self::Available,
            self::Assigned,
            self::Lost,
            self::Disposed,
            self::Archived,
        ];
    }
}