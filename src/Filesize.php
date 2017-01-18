<?php

namespace Mingalevme\Utils;

class Filesize
{
    const UNIT_PREFIXES_POWERS = [
        ''  => 0,
        'b' => 0,
        'B' => 0,
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
     * 
     * 
     * @param int $size Size in bytes
     * @param int|float $precision The optional number of decimal digits to round to, default is 2
     * @param bool $useBinaryPrefix Use power of 2 (1024) instead of power of 10 (1000)
     * @return string Human readable size
     * @throws Exception
     */
    public static function humanize($size, int $precision = 2, bool $useBinaryPrefix = false)
    {
        $base = $useBinaryPrefix ? 1024 : 1000;
        
        foreach (self::UNIT_PREFIXES_POWERS as $prefix => $exp) {
            if ($size < pow($base, $exp + 1)) {
                return round($size / pow($base, $exp), $precision) . $prefix . ($useBinaryPrefix ? 'iB' : 'b');
            }
        }
        
        throw new Exception('Size is too big');
    }
    
    /**
     * GB, G - 1000, GiB - 1024
     * 
     * @param string $size E.g. 300M, 1.5GiB
     * @return int
     * @throws Exception
     */
    public static function dehumanize(string $size)
    {
        if (preg_match('/\d+\.\d+b/', $size)) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        $supportedUnits = implode('', array_keys(self::UNIT_PREFIXES_POWERS));
        $regexp = "/^(\d+(?:\.\d+)?)(([{$supportedUnits}])((?<!b|B)(b|B|iB))?)?$/";
        
        if ((bool) preg_match($regexp, $size, $matches) === false) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        $prefix = isset($matches[3]) ? $matches[3] : 'b';
        
        $base = isset($matches[4]) && $matches[4] === 'iB' ? 1024 : 1000;
        
        if (strpos($matches[1], '.') !== false) {
            return intval(floatval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]));
        } else {
            return intval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]);
        }
    }
}
