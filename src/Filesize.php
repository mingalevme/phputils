<?php

namespace Mingalevme\Utils;

use InvalidArgumentException;

class Filesize
{
    const UNIT_PREFIXES_POWERS = [
        '' => 0,
        'K' => 1,
        'M' => 2,
        'G' => 3,
        'T' => 4,
        'P' => 5,
        'E' => 6,
        'Z' => 7,
        'Y' => 8,
    ];

    /**
     * Convert size in bytes to human-readable filesize (IEC)
     *
     * @param int<0, max>|float $size Size in bytes
     * @param int<0, max> $precision The optional number of decimal digits to round to, default is <b>2</b>
     * @param bool $useBinaryPrefix Use powers-of-two (1024) instead of powers-of-ten (1000), default is <b>false</b>
     * @return non-empty-string Human-readable size
     */
    public static function humanize($size, int $precision = 2, bool $useBinaryPrefix = false): string
    {
        if ($size < 0) {
            throw new InvalidArgumentException('Invalid size');
        }
        $base = $useBinaryPrefix
            ? 1024
            : 1000;
        /** @var int<1, max> $limit */
        $limit = array_values(self::UNIT_PREFIXES_POWERS)[count(self::UNIT_PREFIXES_POWERS) - 1];
        /** @var int<0, max> $power */
        $power = (intval($size) === 0)
            ? 0
            : intval(floor(log($size, $base)));
        if ($power > $limit) {
            $power = $limit;
        }
        $prefix = array_flip(self::UNIT_PREFIXES_POWERS)[$power];
        $multiple = $useBinaryPrefix
            ? "{$prefix}iB"
            : "{$prefix}B";
        return round($size / pow($base, $power), $precision) . $multiple;
    }

    /**
     * Convert human-readable filesize (IEC) to size in bytes
     *
     * https://en.wikipedia.org/wiki/Metric_prefix
     * https://en.wikipedia.org/wiki/Binary_prefix
     *
     * @param string $size Human-readable size
     * @return int|float Size in bytes
     * @throws Exception
     */
    public static function dehumanize(string $size)
    {
        // 1.0B
        if (preg_match('/^\d+\.\d+B$/', $size)) {
            throw new Exception("Invalid size format or unknown/unsupported units: $size");
        }

        $unitsUpper = implode('', array_keys(self::UNIT_PREFIXES_POWERS));
        $unitsLower = strtolower($unitsUpper);

        // 1kiB, 1.0kiB, 1miB, 1.0miB ...
        if (preg_match("/^\d+(\.d+)?[$unitsLower]iB/", $size)) {
            throw new Exception("Invalid size format or unknown/unsupported units: $size");
        }

        $units = "$unitsUpper$unitsLower";
        $regexp = "/^(\d+(?:\.\d+)?)(([$units])?((?<!B)(B|iB))?)?$/";

        if ((bool)preg_match($regexp, $size, $matches) === false) {
            throw new Exception("Invalid size format or unknown/unsupported units: $size");
        }

        $prefix = strtoupper($matches[3] ?? '');

        $base = ($matches[4] ?? '') === 'iB'
            ? 1024
            : 1000;

        if (strpos($matches[1], '.') !== false) {
            return intval(floatval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]));
        }

        return intval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]);
    }
}
