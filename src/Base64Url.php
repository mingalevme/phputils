<?php

namespace Mingalevme\Utils;

class Base64Url
{
    /**
     * Base 64 Encoding with URL and Filename Safe Alphabet
     * 
     * @param string $str
     * @return string
     */
    public static function encode($str)
    {
        return \strtr(\base64_encode($str), [ '+' => '-', '/' => '_', '=' => '' ]);
    }
    
    /**
     * Alias for <b>encode</b>
     * 
     * @param string $str
     * @return string
     */
    public static function e($str)
    {
        return static::encode($str);
    }

    /**
     * Decode a string encoded with base64url algorithm
     * 
     * @param type $str
     * @return type
     */
    public static function decode($str)
    {
        return \base64_decode(\str_pad(strtr($str, '-_', '+/'), \strlen($str) % 4, '=', \STR_PAD_RIGHT)); 
    }
    
    /**
     * Alias for <b>decode</b>
     * 
     * @param string $str
     * @return string
     */
    public static function d($str)
    {
        return static::decode($str);
    }
}
