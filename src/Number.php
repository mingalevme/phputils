<?php

declare(strict_types=1);

namespace Mingalevme\Utils;

/**
 * @readonly
 */
final class Number
{
    public static function isOddNumber($number): bool
    {
        return boolval($number & 1);
    }

    public static function isEvenNumber($number): bool
    {
        return !self::isOddNumber($number);
    }
}