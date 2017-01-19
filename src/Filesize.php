<?php

namespace Mingalevme\Utils;

class Filesize
{
    const UNIT_PREFIXES_POWERS = [
        'B' => 0,
        ''  => 0,
        'K' => 1,
        'k' => 1,
        'M' => 2,
        'G' => 3,
        'T' => 4,
        'P' => 5,
        'E' => 6,
        'Z' => 7,
        'Y' => 8,
    ];
    
    /**
     * Convert size in bytes to human readble filesize (IEC)
     * 
     * @param int $size Size in bytes
     * @param int|float $precision The optional number of decimal digits to round to, default is <b>2</b>
     * @param bool $useBinaryPrefix Use powers-of-two (1024) instead of powers-of-ten (1000), default is <b>false</b>
     * @return string Human readable size
     */
    public static function humanize($size, int $precision = 2, bool $useBinaryPrefix = false)
    {
        $base = $useBinaryPrefix ? 1024 : 1000;
        $limit = array_values(self::UNIT_PREFIXES_POWERS)[count(self::UNIT_PREFIXES_POWERS) - 1];
        $power = ($_ = floor(log($size, $base))) > $limit ? $limit : $_;
        $prefix = array_flip(self::UNIT_PREFIXES_POWERS)[$power];
        $multiple = ($useBinaryPrefix ? strtoupper($prefix) . 'iB' : $prefix . 'B');
        return round($size / pow($base, $power), $precision) . $multiple;
    }
    
    /**
     * Convert human readble filesize (IEC) to size in bytes
     * 
     * https://en.wikipedia.org/wiki/Metric_prefix
     * https://en.wikipedia.org/wiki/Binary_prefix
     * 
     * @param string $size Human readable size
     * @return int Size in bytes
     * @throws Exception
     */
    public static function dehumanize(string $size)
    {
        if (preg_match('/\d+\.\d+B/', $size)) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        if (preg_match('/\d+kiB/', $size)) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        $supportedUnits = implode('', array_keys(self::UNIT_PREFIXES_POWERS));
        $regexp = "/^(\d+(?:\.\d+)?)(([{$supportedUnits}])((?<!B)(B|iB))?)?$/";
        
        if ((bool) preg_match($regexp, $size, $matches) === false) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        $prefix = isset($matches[3]) ? $matches[3] : 'B';
        
        $base = isset($matches[4]) && $matches[4] === 'iB' ? 1024 : 1000;
        
        if (strpos($matches[1], '.') !== false) {
            return intval(floatval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]));
        } else {
            return intval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]);
        }
    }
}
