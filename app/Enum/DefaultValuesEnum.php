<?php
namespace App\Enum;

Enum DefaultValuesEnum
{
    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    public static function getDefaultValuesOptions(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::IN_ACTIVE => 'In Active',
        ];
    }
}
