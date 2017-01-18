<?php

namespace Mingalevme\Utils;

class Filesize
{
    const UNIT_PREFIXES_POWERS = [
        ''  => 0,
        'B' => 0,
        'k' => 1,
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
     * @param int|float $precision The optional number of decimal digits to round to, default is <b>2</b>
     * @param bool $useBinaryPrefix Use powers-of-two (1024) instead of powers-of-ten (1000), default is <b>false</b>
     * @return string Human readable size
     * @throws Exception
     */
    public static function humanize($size, int $precision = 2, bool $useBinaryPrefix = false)
    {
        $base = $useBinaryPrefix ? 1024 : 1000;
        
        foreach (self::UNIT_PREFIXES_POWERS as $prefix => $exp) {
            if ($size < pow($base, $exp + 1)) {
                return round($size / pow($base, $exp), $precision) . ($useBinaryPrefix ? strtoupper($prefix) . 'iB' : $prefix . 'B');
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
