<?php

namespace App\Support;

enum Busyness
{
    case Least;
    case Less;
    case Normal;
    case More;

    public function label(): string
    {
        return match ($this) {
            self::Least => 'Empty',
            self::Less => 'Not Busy',
            self::Normal => 'Medium Busy',
            self::More => 'Very  Busy'
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Least => 'zinc',
            self::Less => 'green',
            self::Normal => 'cyan',
            self::More => 'rose'
        };
    }
}
