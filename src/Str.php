<?php

namespace Mingalevme\Utils;

class Str extends \Illuminate\Support\Arr
{
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
}
