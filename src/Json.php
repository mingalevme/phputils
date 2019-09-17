<?php

namespace Mingalevme\Utils;

use Mingalevme\Utils\Json\Exception\ParseException;

class Json
{
    /**
     * Encode data to JSON with \JSON_UNESCAPED_SLASHES and \JSON_UNESCAPED_UNICODE
     * 
     * @param mixed $data
     * @return string
     */
    public static function encode($data)
    {
        return \json_encode($data, \JSON_UNESCAPED_SLASHES|\JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Alias to encode
     * 
     * @param mixed $data
     * @return string
     */
    public static function e($data)
    {
        return static::encode($data);
    }
    
    /**
     * Decode JSON to assoc (by default) data
     * 
     * @param string $json
     * @param bool $assoc
     * @return mixed
     * @throws ParseException in case of error
     */
    public static function decode($json, $assoc=true)
    {
        $data = \json_decode($json, $assoc);
        
        switch (json_last_error()) {
            case \JSON_ERROR_NONE:
                $error = null;
                break;
            case \JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case \JSON_ERROR_STATE_MISMATCH:
                $error = 'State mismatch (invalid or malformed JSON)';
                break;
            case \JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded';
                break;
            case \JSON_ERROR_SYNTAX:
                $error = 'Syntax error';
                break;
            case \JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
        }
        
        if ($error) {
            throw new ParseException($error);
        }
        
        return $data;
    }
    
    /**
     * Alias to decode
     * 
     * @param string $data
     * @param bool $assoc
     * @return mixed
     * @throws ParseException
     */
    public static function d($data, $assoc=true)
    {
        return static::decode($data, $assoc);
    }
}
