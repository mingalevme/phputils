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
     * @param int $length
     * @return string
     * @throws \Exception if it was not possible to gather sufficient entropy.
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

        return \strlen($str) > $length
            ? substr($str, 0, $length)
            : $str;
    }

    /**
     * Transform string from camelCase to snake_case
     *
     * @param string $str
     * @return string
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
     * @return string
     */
    public static function camelize($str, $mode = self::LOWER)
    {
        $result = \str_replace(['-', '_'], '', \ucwords(\ucwords(strtolower($str)), '-_'));

        return $mode === self::UPPER ? \ucfirst($result) : \lcfirst($result);
    }

    public static function randomHumanized($length)
    {
        $conso = ["b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z"];
        $vocal = ["a","e","i","o","u"];

        srand((double) microtime() * 1000000);

        $max = $length/2;

        $password = '';

        for ($i=1; $i <= $max; $i++) {
            $password .= $conso[rand(0,19)];
            $password .= $vocal[rand(0,4)];
        }

        return $password;
    }

    public static function explode($delimiter, $string, $limit = null)
    {
        return array_map('trim', $limit !== null ? explode($delimiter, $string, $limit) : explode($delimiter, $string));
    }

    public static function clean($str)
    {
        $str = iconv("UTF-8", "UTF-8//IGNORE", $str); // drop all non utf-8 characters
        /*
         * &nbsp; ( ): 194.160
         * &brvbar; (¦): 194.166
         * &copy; (©): 194.169
         * &laquo; («): 194.171
         * &reg; (®): 194.174
         * &plusmn; (±): 194.177
         * &micro; (µ): 194.181
         * &para; (¶): 194.182
         * &middot; (·): 194.183
         * &raquo; (»): 194.187
         * &ensp; ( ): 226.128.130
         * &ndash; (–): 226.128.147
         * &mdash; (—): 226.128.148
         * &lsquo; (‘): 226.128.152
         * &rsquo; (’): 226.128.153
         * &sbquo; (‚): 226.128.154
         * &ldquo; (“): 226.128.156
         * &rdquo; (”): 226.128.157
         * &bdquo; („): 226.128.158
         * &dagger; (†): 226.128.160
         * &Dagger; (‡): 226.128.161
         * &bull; (•): 226.128.162
         * &hellip; (…): 226.128.166
         * &permil; (‰): 226.128.176
         * &lsaquo; (‹): 226.128.185
         * &euro; (€): 226.130.172
         * &trade; (™): 226.132.162
         *
         * To remove:
         * 00-31                -> [\x00-\x1F]
         * 194.[128-159]        -> \xC2[\x80-\x9F]
         * 226.128.[168-169]    -> \xE2\x80[\xA8-\xA9]
         * 226.130.[149-159]    -> \xE2\x82[\x05-\x9F]
         * 226.131.[128-143]    -> \xE2\x83[\x80-\x8F]
         * 226.131.[177-191]    -> \xE2\x83[\xB1-\xBF]
         * 226.129.[159-175]    -> \xE2\x81[\x9F-\xAF]
         * 226.144.[167-191]    -> \xE2\x90[\xA7-\xBF]
         *
         */
        $str = preg_replace('/(?>[\x00-\x1F]|\xC2[\x80-\x9F]|\xE2\x80[\xA8-\xA9]|\xE2\x82[\x05-\x9F]|\xE2\x83[\x80-\x8F]|\xE2\x83[\xB1-\xBF]|\xE2\x81[\x9F-\xAF]|\xE2\x90[\xA7-\xBF])/', '', $str);
        $str = trim($str);
        return $str;
    }

    public static function ascii($str)
    {
        return array_map(function($x) {
            return strval(ord($x));
        }, str_split($str));
    }
}
