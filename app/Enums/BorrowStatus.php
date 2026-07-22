<?php

namespace App\Enums;

enum BorrowStatus: string
{
    case Borrowed = 'borrowed';
    case Returned = 'returned';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Borrowed => 'Borrowed',
            self::Returned => 'Returned',
            self::Lost => 'Lost',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Borrowed => 'blue',
            self::Returned => 'green',
            self::Lost => 'red',
        };
    }
}