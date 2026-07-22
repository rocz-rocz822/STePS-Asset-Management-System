<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case Fair = 'fair';
    case NeedsRepair = 'needs_repair';
    case Damaged = 'damaged';
    case BeyondRepair = 'beyond_repair';
    case Disposed = 'disposed';

    public function label(): string
    {
        return match ($this) {
            self::Excellent => 'Excellent',
            self::Good => 'Good',
            self::Fair => 'Fair',
            self::NeedsRepair => 'Needs Repair',
            self::Damaged => 'Damaged',
            self::BeyondRepair => 'Beyond Repair',
            self::Disposed => 'Disposed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Excellent, self::Good => 'green',
            self::Fair => 'yellow',
            self::NeedsRepair, self::Damaged => 'yellow',
            self::BeyondRepair, self::Disposed => 'red',
        };
    }
}