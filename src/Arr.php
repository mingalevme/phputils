<?php namespace Mingalevme\Utils;

class Arr
{
    /**
     * Rename array key
     * 
     * @param $a
     * @param $old
     * @param $new
     * @return mixed
     */
    public static function rename(&$a, $old, $new)
    {
        $a[$new] = $a[$old];
        unset ($a[$old]);
        return $old;
    }

    /**
     * Recursively delete unwanted (default: null) values from array
     * 
     * @param array $arr
     * @param mixed $values
     * @return null
     */
    public static function filter(array &$arr, $values=NULL)
    {
        if ($values === NULL || is_array($values) === FALSE) {
            $values = [$values];
        }
        
        foreach ($arr as $k => &$v) {
            if (is_array($v)) {
                static::filter($v, $values);
            }
            if (in_array($v, $values, true)) {
                unset($arr[$k]);
            }
        }
    }

    /**
     * Returns the first element of array
     * 
     * @param $a
     * @return mixed
     */
    public static function first($a)
    {
        return static::index($a, 0);
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

    /**
     * Safely gets array element by key
     * 
     * @param array $arr
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function get(array $arr, $key, $default=null)
    {
        return static::index($arr, $key) ?: $default;
    }

    /**
     * Makes the array where keys is the subarray values by $key and values is the subarrays.
     * If $valueAttr is specified, the result array values 
     * 
     * @param array $array Array of associative arrays
     * @param string $keyAttr
     * @param string $valueAttr
     * @return array
     */
    public static function toMap($array, $keyAttr, $valueAttr=null)
    {
        $result = [];

        foreach ($array as $data) {
            if (array_key_exists($keyAttr, $data) === FALSE) {
                continue;
            }
            if ($valueAttr) {
                if (array_key_exists($valueAttr, $data)) {
                    $result[$data[$keyAttr]] = $data[$valueAttr];
                } else {
                    $result[$data[$keyAttr]] = NULL;
                }
            } else {
                $result[$data[$keyAttr]] = $data;
            }
        }

        return $result;
    }
    
    /**
     * Returns array element and deletes it from array
     * 
     * @param array $arr
     * @param int|string $index
     * @return mixed
     */
    public static function rob(&$arr, $index)
    {
        $v = $arr[$index];
        unset($arr[$index]);
        return $v;
    }
}
