<?php

namespace App\Enums;

enum AssetHistoryAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Updated => 'Updated',
            self::Deleted => 'Deleted',
            self::Restored => 'Restored',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Created => 'green',
            self::Updated => 'blue',
            self::Deleted => 'red',
            self::Restored => 'yellow',
        };
    }
}