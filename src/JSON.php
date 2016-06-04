<?php namespace Mingalevme\Utils;

class JSON
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
     * Decode JSON to assoc data
     * 
     * @param string $json
     * @return mixed
     */
    public static function decode($json)
    {
        return \json_decode($json, true);
    }
    
    /**
     * Alias to decode
     * 
     * @param type $data
     * @return type
     */
    public static function d($data)
    {
        return static::decode($data);
    }
}
