<?php

namespace Mingalevme\Utils;

class Str
{
    const LOWER = 'lower';
    const UPPER = 'upper';
    
    /**
     * (PHP 4, PHP 5, PHP 7)<br/>
     * Find the position of the first occurrence of a substring (or first
     * of substring if <i>needle</i> is an array) in a string
     * @link http://php.net/manual/en/function.strpos.php
     * @param string $haystack <p>
     * The string to search in.
     * </p>
     * @param mixed $needle <p>
     * If <i>needle</i> is not a string or an array, it is converted
     * to an integer and applied as the ordinal value of a character.
     * </p>
     * @param int $offset [optional] <p>
     * If specified, search will start this number of characters counted from
     * the beginning of the string. Unlike <b>strrpos</b> and
     * <b>strripos</b>, the offset cannot be negative.
     * </p>
     * @return mixed the position of where the needle exists relative to the beginning of
     * the <i>haystack</i> string (independent of offset).
     * Also note that string positions start at 0, and not 1.
     * </p>
     * <p>
     * Returns <b>false</b> if the needle was not found.
     */
    public static function strpos($haystack, $needle, $offset = 0)
    {
        foreach ((array) $needle as $substr) {
            if (false !== ($index = \strpos($haystack, $substr, $offset))) {
                return $index;
            }
        }
        
        return false;
    }
    
    /**
     * Alias of <strong>static::strpos</strong>
     * 
     * @param string $haystack
     * @param mixed $needle string or array of strings
     * @param int $offset
     * @return bool
     */
    public static function pos($haystack, $needle, $offset = 0)
    {
        return static::strpos($haystack, $needle, $offset);
    }
    
    /**
     * Check if <strong>$haystack</strong> contains <strong>$needle</strong>
     * or one of <strong>$needle</strong> if $needle is an array
     * 
     * @param string $haystack
     * @param mixed $needle string or array of strings
     * @param int $offset
     * @return bool
     */
    public static function contains($haystack, $needle, $offset = 0)
    {
        return static::strpos($haystack, $needle, $offset) !== false;
    }
    
    /**
     * Generate a safe random string
     * 
     * @param type $length
     * @return type
     */
    public static function random($length)
    {
        if (function_exists('\random_bytes')) {
            $str = \bin2hex(\random_bytes(($length & 1 ? $length + 1 : $length)/2));
        } elseif (\function_exists('\openssl_random_pseudo_bytes')) {
            $str = \bin2hex(\openssl_random_pseudo_bytes(($length & 1 ? $length + 1 : $length)/2));
        } elseif (\function_exists('\mcrypt_create_iv')) {
            $str = \bin2hex(\mcrypt_create_iv($length, \MCRYPT_DEV_URANDOM));
        } else {
            $str = \str_shuffle(\substr(\str_repeat(\md5(\mt_rand()), 2 + $length/32), 0, $length));
        }
        
        return \strlen($str) > $length ? substr($str, 0, $length) : $str;
    }
    
    /**
     * Transform string from camelCase to snake_case
     * 
     * @param string $str
     * @return type
     */
    public static function snakeize($str, $mode = self::LOWER)
    {
        $whitespaceless = \preg_replace('/\s+?/', '_', $str);
        $decamelized = \preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $whitespaceless);
        $underscoreless = preg_replace('/_{2,}/', '_', $decamelized);
        return $mode === self::UPPER ? \ucfirst(\strtolower($underscoreless)) : \strtolower($underscoreless);
    }

    /**
     * Transform string from snake_case to camelCase
     * 
     * @param string $str
     * @return type
     */
    public static function camelize($str, $mode = self::LOWER)
    {
        $result = \str_replace(['-', '_'], '', \ucwords(\ucwords(strtolower($str)), '-_'));
        
        return $mode === self::UPPER ? \ucfirst($result) : \lcfirst($result);
    }
}
