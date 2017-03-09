<?php

namespace Mingalevme\Utils;

class Str
{
    const LOWER = 'lower';
    const UPPER = 'upper';
    
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
