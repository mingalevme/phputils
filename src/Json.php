<?php

namespace Mingalevme\Utils;

class Json
{
    /**
     * Encode data to JSON with options:
     * \JSON_UNESCAPED_SLASHES
     * \JSON_UNESCAPED_UNICODE
     * 
     * @param mixed $data
     */
    public static function encode($data)
    {
        return \json_encode($data, \JSON_UNESCAPED_SLASHES|\JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Alias to encode
     * 
     * @param type $data
     * @return type
     */
    public static function e($data)
    {
        return static::encode($data);
    }
    
    /**
     * Decode JSON to assoc (by default) data
     * 
     * @param string $json
     * @return mixed
     */
    public static function decode($json, $assoc=true)
    {
        return \json_decode($json, $assoc);
    }
    
    /**
     * Alias to decode
     * 
     * @param type $data
     * @return type
     */
    public static function d($data, $assoc=true)
    {
        return static::decode($data, $assoc);
    }
}
