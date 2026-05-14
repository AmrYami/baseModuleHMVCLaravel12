<?php

namespace App\Enum;

enum DefaultValuesEnum: int
{
    case ACTIVE = 1;
    case IN_ACTIVE = 0;

    public static function getDefaultValuesOptions(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::IN_ACTIVE->value => 'Inactive',
        ];
    }
}
