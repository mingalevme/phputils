<?php

namespace Mingalevme\Utils;

class Arr extends \Illuminate\Support\Arr
{
    /**
     * Rename array key
     * 
     * @param array $arr
     * @return array
     */
    public static function rename(&$arr, $old, $new)
    {
        $arr[$new] = $arr[$old];
        unset($arr[$old]);
    }
    
    /**
     * @deprecated Use <b>compress</b> instead
     * 
     * @param array $arr
     * @return array
     */
    public static function compact($arr)
    {
        return static::compress($arr);
    }
    
    /**
     * Return a copy of $arr without any falsy values (including empty arrays)
     * 
     * @param array $arr
     * @return array
     */
    public static function compress($arr)
    {
        return static::filter($arr, function($value){
            return (bool) $value;
        });
    }

    /**
     * Recursively filters elements of an array using a callback function
     * 
     * @param array $arr
     * @param callable $callback
     * @return null
     */
    public static function filter(array $arr, callable $callback)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value) && count($value) > 0) {
                $arr[$key] = static::filter($value, $callback);
            }
            
            if ((bool) call_user_func($callback, $arr[$key], $key) === false) {
                unset($arr[$key]);
            }
        }
        
        return count($arr) ? $arr : null;
    }

    /**
     * Looks for a value by index.
     * Works with numeric index on associative arrays.
     * 
     * @param $arr
     * @param $index
     * @return mixed
     */
    public static function index($arr, $index)
    {
        if (is_array($arr) == FALSE) {
            return $arr;
        }

        if (count($arr) == 0) {
            return NULL;
        }

        if (array_key_exists($index, $arr)) {
            return $arr[$index];
        }
        
        $values = array_values($arr);
        
        if (array_key_exists($index, $values)) {
            return $values[$index];
        }
        
        return NULL;
    }
    
    public static function max($arr, $key)
    {
        $result = null;
        
        foreach ($arr as $item) {
            if (static::accessible($item) === false) {
                continue;
            } elseif ($result === null) {
                $result = $item;
            } elseif (static::get($item, $key) > static::get($result, $key)) {
                $result = $item;
            }
        }
        
        return $result;
    }
    
    /**
     * Makes a new array where each item is $key . $sep . $value of each item of source array
     * 
     * @param array $arr
     * @param string $kvsep key => value separator
     * @param string $psep "$key.$sep.$value" separator
     * @return string
     */
    public static function toString(array $arr, $kvsep, $psep)
    {
        $result = [];
        
        foreach ($arr as $k => $v) {
            $result[] = "{$k}{$kvsep}{$v}";
        }
        
        return implode($psep, $result);
    }

    /**
     * Looks for an array inside input array by key and value
     *
     * @param array $array
     * @param $key
     * @param $value
     * @return bool
     */
    public static function hasObjectWithKeyAndValue(array $array, $key, $value)
    {
        foreach ($array as $object) {
            if (is_array($object) && array_key_exists($key, $object) && $object[$key] === $value) {
                return true;
            }
        }

        return false;
    }
}
