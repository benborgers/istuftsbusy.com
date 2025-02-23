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
            self::Less => 'Less Busy',
            self::Normal => 'Normal',
            self::More => 'Busier'
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Least => 'gray',
            self::Less => 'green',
            self::Normal => 'cyan',
            self::More => 'rose'
        };
    }
}
