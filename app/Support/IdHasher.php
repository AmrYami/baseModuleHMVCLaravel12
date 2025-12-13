<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Small helper to produce short, opaque hashes for integer IDs.
 */
class IdHasher
{
    protected static string $alphabet;
    protected static int $minLength;
    protected static int $saltInt;

    protected static function boot(): void
    {
        if (isset(self::$alphabet)) return;

        $salt     = (string) config('id.salt', config('app.key'));
        self::$minLength = (int) config('id.min_length', 8);
        self::$alphabet  = (string) config('id.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        self::$saltInt   = abs(crc32($salt));
        if (self::$saltInt === 0) {
            self::$saltInt = 1;
        }
    }

    protected static function encodeInt(int $n): string
    {
        $alphabet = self::$alphabet;
        $base = strlen($alphabet);
        if ($n === 0) {
            return $alphabet[0];
        }
        $out = '';
        $num = $n;
        while ($num > 0) {
            $rem = $num % $base;
            $num = intdiv($num, $base);
            $out = $alphabet[$rem] . $out;
        }
        return $out;
    }

    protected static function decodeInt(string $hash): ?int
    {
        $alphabet = self::$alphabet;
        $base = strlen($alphabet);
        $chars = str_split($hash);
        $num = 0;
        foreach ($chars as $ch) {
            $pos = strpos($alphabet, $ch);
            if ($pos === false) {
                return null;
            }
            $num = $num * $base + $pos;
        }
        return $num;
    }

    /**
    * Encode a model or integer into a short hash.
    */
    public static function encode(int|string|Model|null $value): string
    {
        self::boot();
        if ($value instanceof Model) {
            $value = $value->getKey();
        }

        if ($value === null || $value === '' || !is_numeric($value)) {
            return '';
        }

        $obfuscated = ((int) $value) ^ self::$saltInt;
        $encoded = self::encodeInt($obfuscated);

        // Left-pad to minimum length using first alphabet char (no impact on value)
        if (strlen($encoded) < self::$minLength) {
            $encoded = str_pad($encoded, self::$minLength, self::$alphabet[0], STR_PAD_LEFT);
        }

        return $encoded;
    }

    /**
    * Decode a hash (or passthrough integer) back to an int. Returns null on failure.
    */
    public static function decode(int|string|null $value): ?int
    {
        self::boot();
        if ($value === null || $value === '') {
            return null;
        }
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        $decoded = self::decodeInt((string) $value);
        if ($decoded === null) {
            return null;
        }
        return $decoded ^ self::$saltInt;
    }
}
